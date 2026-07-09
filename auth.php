<?php

function auth_password_is_hash($stored_password)
{
    if (!is_string($stored_password) || $stored_password === '') {
        return false;
    }

    $info = password_get_info($stored_password);
    return !empty($info['algo']);
}

function auth_hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function auth_verify_password($password, $stored_password, &$needs_rehash = false)
{
    $needs_rehash = false;

    if (!is_string($stored_password) || $stored_password === '') {
        return false;
    }

    if (auth_password_is_hash($stored_password)) {
        $valid = password_verify($password, $stored_password);
        $needs_rehash = $valid && password_needs_rehash($stored_password, PASSWORD_DEFAULT);
        return $valid;
    }

    $valid = hash_equals($stored_password, $password);
    $needs_rehash = $valid;
    return $valid;
}

function auth_client_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function auth_user_agent()
{
    return substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
}

function auth_ensure_attempt_table($mysqli)
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $sql = "
        CREATE TABLE IF NOT EXISTS login_attempts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            success TINYINT(1) NOT NULL DEFAULT 0,
            reason VARCHAR(64) NOT NULL DEFAULT '',
            user_agent VARCHAR(255) NOT NULL DEFAULT '',
            attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_login_attempts_user_time (username, attempted_at),
            KEY idx_login_attempts_ip_time (ip_address, attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $mysqli->query($sql);
    $ready = true;
}

function auth_record_login_attempt($mysqli, $username, $success, $reason = '')
{
    auth_ensure_attempt_table($mysqli);

    $username = substr(trim((string)$username), 0, 255);
    $ip = auth_client_ip();
    $agent = auth_user_agent();
    $success_value = $success ? 1 : 0;
    $reason = substr((string)$reason, 0, 64);

    $stmt = $mysqli->prepare("
        INSERT INTO login_attempts (username, ip_address, success, reason, user_agent)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssiss", $username, $ip, $success_value, $reason, $agent);
    $stmt->execute();
    $stmt->close();
}

function auth_login_is_rate_limited($mysqli, $username)
{
    auth_ensure_attempt_table($mysqli);

    $username = trim((string)$username);
    $ip = auth_client_ip();
    $window_minutes = 15;
    $max_failures = 5;

    $stmt = $mysqli->prepare("
        SELECT COUNT(*) AS failed_attempts
        FROM login_attempts
        WHERE success = 0
          AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
          AND (username = ? OR ip_address = ?)
    ");
    $stmt->bind_param("iss", $window_minutes, $username, $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return ((int)($row['failed_attempts'] ?? 0)) >= $max_failures;
}

function auth_upgrade_password_hash($mysqli, $user_id, $password)
{
    $hash = auth_hash_password($password);
    $stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ? WHERE USER_ID = ?");
    $stmt->bind_param("si", $hash, $user_id);
    $stmt->execute();
    $stmt->close();
}

