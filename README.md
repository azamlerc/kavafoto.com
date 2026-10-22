# kavafoto.com

A read-only archive of the original website. The PHP data files and historical content remain in their original format.

## Runtime

The Dockerfile uses `php:8.5.12-apache` (the patched stable release available on October 22, 2026). Update the patch tag and rebuild when PHP publishes security updates; changing source files does not update an already-running Fly machine.

Apache listens on port 80. Extensionless historical links are supported using Apache rewrite rules. PHP reports all errors to server logs without exposing them to visitors.

## Local verification

```sh
docker build --pull -t kavafoto.com:archive .
docker run --rm --name kavafoto-archive -p 127.0.0.1:8080:80 kavafoto.com:archive
# From another terminal:
python3 tests/smoke.py http://127.0.0.1:8080
docker logs kavafoto-archive
```

Check the logs for PHP warnings, deprecations, and fatal errors as well as the HTTP results. The image intentionally excludes tests and Git metadata.

## Fly.io

Use the existing Fly application's configuration with its internal HTTP port set to **80**, and deploy this Dockerfile with a fresh build. This checkout does not contain `fly.toml`; retain or retrieve the existing application's settings rather than creating another app. For example, from this directory, use `fly deploy --app <existing-app-name>` with your existing Fly configuration. Verify the deployed runtime is PHP 8.5.12 and rerun the smoke checks against the HTTPS domain after deployment.

## Photo metadata

The catalog and membership data remain local PHP arrays. Photo images and per-folder metadata still come from the configured `https://images.andrewzc.net/shoebox/` asset host; browsing retains that existing dependency. Empty searches return a clear 404 instead of crashing. Legacy constructors and class-level helpers now work with PHP 8.
