# SendGrid password-reset email

Password-reset messages are sent directly to the Twilio SendGrid Mail Send API
over HTTPS. The application does not fall back to PHP `mail()` when SendGrid is
unavailable.

## SendGrid setup

1. Authenticate `preferredequine.com` under **Settings > Sender Authentication**
   and add the DNS records supplied by SendGrid.
2. Create a dedicated API key with only **Mail Send > Full Access** permission.
3. Add these settings to the production `.env` file:

   ```dotenv
   SENDGRID_API_KEY=SG.replace_with_the_production_key
   SENDGRID_FROM_EMAIL=noreply@preferredequine.com
   SENDGRID_FROM_NAME="Preferred Equine"
   SENDGRID_REPLY_TO=support@preferredequine.com
   ```

The From address must belong to the authenticated SendGrid domain. The Reply-To
setting is optional and is omitted when it is empty or invalid.

## Production requirements

PHP must have either the cURL extension or HTTPS stream support through OpenSSL
and `allow_url_fopen`. Outbound HTTPS (TCP 443) to `api.sendgrid.com` must be
allowed. No local mail daemon or outbound SMTP port is required.

## Diagnostics

SendGrid success and failure details go to the PHP error log. A successful API
submission includes `SendGrid accepted password reset email` and, when supplied
by SendGrid, its message ID. Errors include the HTTP status and SendGrid's
sanitized response, but never the API key or reset code.

Only HTTP `202 Accepted` counts as successful submission. If submission fails,
the newly generated reset code is removed from the database so an undelivered
code cannot replace a previously valid reset request.
