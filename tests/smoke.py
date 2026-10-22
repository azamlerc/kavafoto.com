#!/usr/bin/env python3
"""HTTP smoke checks against a running Docker image. Uses Python's standard library."""
import concurrent.futures
import sys
import urllib.error
import urllib.request

CASES = [('', 200, 'All rights reserved.'), ('category.php?id=1658869099-295857131', 200, 'All rights reserved.'), ('category.php?id=1841320009-1416989116', 200, 'All rights reserved.'), ('category?search=amsterdam', 200, 'All rights reserved.'), ('category.php?search=zzzznomatch', 404, 'No photos found.'), ('category.php?id=invalid', 404, 'Category not found.'), ('category.php?id[]=1', 400, 'Invalid query'), ('category.php?page=no', 400, 'Invalid page'), ('Shoebox/index.php', 200, '</html>'), ('Shoebox/index.php?id=1658869099-295857131', 200, '</html>'), ('Shoebox/browser/index.php', 200, '</html>'), ('Shoebox/browser/browser.php', 200, '</html>'), ('Shoebox/browser/results.php', 200, '</html>'), ('Shoebox/browser/results.php?id=1658869099-295857131&page=2', 200, '</html>'), ('Shoebox/browser/results.php?search=zzzznomatch', 200, '</html>'), ('Shoebox/browser/photo.php?id=1658869099-295857131&number=1', 200, '</html>'), ('Shoebox/browser/toolbar.php?id=1658869099-295857131', 200, '</html>'), ('Shoebox/browser/column.php?id=0000000001', 200, '</html>'), ('concerts/', 200, 'All rights reserved.'), ('concerts/?filter=Jazz', 200, 'All rights reserved.'), ('blog/', 200, 'All rights reserved.'), ('websites/?site=jeremiah', 200, 'All rights reserved.'), ('websites/?site=invalid', 404, 'Website not found.')]
BASE = (sys.argv[1] if len(sys.argv) > 1 else 'http://127.0.0.1:8080').rstrip('/')

def check(case):
    path, expected_status, expected_text = case
    try:
        response = urllib.request.urlopen(BASE + '/' + path, timeout=90)
    except urllib.error.HTTPError as error:
        response = error
    with response:
        body = response.read().decode('utf-8', 'replace')
        assert response.status == expected_status, (path, response.status, expected_status)
        assert expected_text.lower() in body.lower(), (path, 'incomplete page')
        assert not any(marker in body for marker in ('<b>Fatal error</b>', '<b>Warning</b>', '<b>Deprecated</b>')), path
    return path

with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
    results = list(pool.map(check, CASES))
print(f'{len(results)} HTTP checks passed.')
