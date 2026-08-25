<?php

/**
 * Send transactional email through the Twilio SendGrid v3 Mail Send API.
 *
 * The function deliberately has no mail() fallback: a local mail handoff can
 * report success even when the message cannot leave the server.
 */
function sendgrid_send_email($to, $subject, $html_body, $text_body = '')
{
    $api_key = trim((string)($_ENV['SENDGRID_API_KEY'] ?? getenv('SENDGRID_API_KEY') ?: ''));
    $from_email = trim((string)($_ENV['SENDGRID_FROM_EMAIL'] ?? getenv('SENDGRID_FROM_EMAIL') ?: ''));
    $from_name = trim((string)($_ENV['SENDGRID_FROM_NAME'] ?? getenv('SENDGRID_FROM_NAME') ?: 'Preferred Equine'));
    $reply_to = trim((string)($_ENV['SENDGRID_REPLY_TO'] ?? getenv('SENDGRID_REPLY_TO') ?: ''));

    if ($api_key === '' || !filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        error_log('SendGrid mail configuration error: SENDGRID_API_KEY and a valid SENDGRID_FROM_EMAIL are required');
        return false;
    }

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log('SendGrid mail error: invalid recipient address');
        return false;
    }

    $content = [];
    if ($text_body !== '') {
        $content[] = ['type' => 'text/plain', 'value' => $text_body];
    }
    $content[] = ['type' => 'text/html', 'value' => $html_body];

    $payload = [
        'personalizations' => [[
            'to' => [['email' => $to]],
        ]],
        'from' => [
            'email' => $from_email,
            'name' => $from_name,
        ],
        'subject' => $subject,
        'content' => $content,
    ];

    if (filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
        $payload['reply_to'] = ['email' => $reply_to];
    }

    try {
        $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    } catch (JsonException $e) {
        error_log('SendGrid mail error: could not encode request payload');
        return false;
    }

    if (function_exists('curl_init')) {
        return sendgrid_send_with_curl($api_key, $json);
    }

    return sendgrid_send_with_stream($api_key, $json);
}

function sendgrid_send_with_curl($api_key, $json)
{
    $response_headers = [];
    $curl = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_USERAGENT => 'PreferredEquine/1.0',
        CURLOPT_HEADERFUNCTION => function ($curl_handle, $header_line) use (&$response_headers) {
            $separator = strpos($header_line, ':');
            if ($separator !== false) {
                $name = strtolower(trim(substr($header_line, 0, $separator)));
                $response_headers[$name] = trim(substr($header_line, $separator + 1));
            }
            return strlen($header_line);
        },
    ]);

    if (defined('CURLOPT_PROTOCOLS') && defined('CURLPROTO_HTTPS')) {
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
    }

    $response_body = curl_exec($curl);
    $curl_error = curl_error($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($response_body === false) {
        error_log('SendGrid transport error: ' . sendgrid_log_value($curl_error));
        return false;
    }

    return sendgrid_response_succeeded($status, $response_headers, $response_body);
}

function sendgrid_send_with_stream($api_key, $json)
{
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", [
                'Authorization: Bearer ' . $api_key,
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: PreferredEquine/1.0',
            ]),
            'content' => $json,
            'timeout' => 15,
            'ignore_errors' => true,
        ],
    ]);

    $response_body = @file_get_contents('https://api.sendgrid.com/v3/mail/send', false, $context);
    $raw_headers = isset($http_response_header) ? $http_response_header : [];

    if ($response_body === false) {
        $last_error = error_get_last();
        $detail = $last_error['message'] ?? 'unknown HTTPS transport failure';
        error_log('SendGrid transport error: ' . sendgrid_log_value($detail));
        return false;
    }

    $status = 0;
    $response_headers = [];
    foreach ($raw_headers as $header_line) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header_line, $matches)) {
            $status = (int)$matches[1];
            continue;
        }
        $separator = strpos($header_line, ':');
        if ($separator !== false) {
            $name = strtolower(trim(substr($header_line, 0, $separator)));
            $response_headers[$name] = trim(substr($header_line, $separator + 1));
        }
    }

    return sendgrid_response_succeeded($status, $response_headers, $response_body);
}

function sendgrid_response_succeeded($status, $headers, $response_body)
{
    $message_id = $headers['x-message-id'] ?? '';
    if ($status === 202) {
        error_log('SendGrid accepted password reset email' . ($message_id !== '' ? '; message_id=' . sendgrid_log_value($message_id) : ''));
        return true;
    }

    error_log(
        'SendGrid rejected password reset email; status=' . (int)$status .
        '; response=' . sendgrid_log_value($response_body)
    );
    return false;
}

function sendgrid_log_value($value)
{
    $value = preg_replace('/[\r\n\x00-\x1F\x7F]+/', ' ', (string)$value);
    return substr($value, 0, 1000);
}
