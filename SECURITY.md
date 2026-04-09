# Security Policy

GeniXCMS is committed to ensuring the safety and security of its users and their web applications. We take all security vulnerabilities seriously and aim to address them promptly.

## Supported Versions

Currently, the following versions are under active security support:

| Version | Status | Security Patches |
| ------- | ------ | ---------------- |
| 2.x     | Active (In Development) | ✅ Fully Supported |
| 1.1.x   | LTS | ✅ Critical Fixes Only |
| < 1.1   | EOL | ❌ Not Supported |

## Security Features in GeniXCMS v2.x

GeniXCMS v2.x implements several core security layers:
- **Prepared Statements**: All database interactions use PDO prepared statements with placeholders to mitigate SQL Injection risks.
- **XSS Protection**: Integrated **HTMLPurifier** to sanitize user-generated content and **Latte** templating to auto-escape frontend outputs.
- **Access Control**: Strict `GX_LIB` check on all system files to prevent direct execution.
- **Server Hardening**: Included `.htaccess` and `nginx.conf` examples that implement HTTP security headers (CSP, X-Frame-Options, HSTS).

## Reporting a Vulnerability

If you discover a security vulnerability within GeniXCMS, please notify us immediately. 

**Do NOT report security vulnerabilities through public GitHub issues.**

Please send a detailed report to:
📧 **genixcms@gmail.com**

Include the following information in your report:
1. Nature of the vulnerability.
2. Steps to reproduce the issue.
3. Potential impact.

We will acknowledge your report, investigate the issue, and work on a patch as quickly as possible. We request that you follow responsible disclosure guidelines and give us reasonable time to address the issue before making it public.

Thank you for helping keep GeniXCMS secure!
