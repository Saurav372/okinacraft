import urllib.request
import urllib.parse
import http.cookiejar
import json
import re
import sys

BASE = "http://localhost:8080"
passed = 0
failed = 0

def check(name, fn):
    global passed, failed
    try:
        fn()
        passed += 1
        print(f"  [PASS] {name}")
    except Exception as e:
        failed += 1
        print(f"  [FAIL] {name}: {e}")

print("==================================================")
print("     OKINA CRAFT E2E VERIFICATION TEST SUITE      ")
print("==================================================")

# 1. Homepage
def t_home():
    with urllib.request.urlopen(BASE + "/") as res:
        assert res.status == 200
        html = res.read().decode('utf-8')
        assert "Okina Craft" in html
        assert "how-to-buy.mp4" in html
check("Storefront Homepage loads with Okina Craft branding", t_home)

# 2. PDP
def t_pdp():
    with urllib.request.urlopen(BASE + "/products/custom-design-240-gsm-premium-oversized-t-shirt-100-french-terry-cotton") as res:
        assert res.status == 200
        html = res.read().decode('utf-8')
        assert "Custom Design 240 GSM Premium Oversized T-Shirt" in html
        assert "custom_design" in html or "file" in html
check("PDP Custom Product loads with artwork upload", t_pdp)

# 3. API predictive search
def t_search_api():
    with urllib.request.urlopen(BASE + "/api/search/suggest?q=oversized") as res:
        assert res.status == 200
        data = json.loads(res.read().decode('utf-8'))
        assert data.get("ok") is True
        assert len(data.get("products", [])) > 0
check("Predictive Search API returns JSON product suggestions", t_search_api)

# 4. Tabler Admin Assets & Okina Brand Logos
def t_brand_assets():
    for path in [
        "/assets/vendor/tabler/tabler.min.css",
        "/assets/vendor/tabler/tabler.min.js",
        "/assets/vendor/tabler/tabler-icons.min.css",
        "/assets/vendor/tabler/fonts/tabler-icons.core.woff2",
        "/assets/media/okina-mark.png",
        "/assets/media/logo.png",
        "/assets/media/logo-light.png",
        "/assets/media/logo-dark-horizontal.png",
    ]:
        with urllib.request.urlopen(BASE + path) as res:
            assert res.status == 200, f"Failed to load {path}"
check("Okina Craft brand assets & Tabler vendor files load (HTTP 200)", t_brand_assets)

# 5. Admin Login Page UI
def t_admin_login_ui():
    with urllib.request.urlopen(BASE + "/admin/login") as res:
        assert res.status == 200
        html = res.read().decode('utf-8')
        assert "OKINA CRAFT" in html
        assert "tabler.min.css" in html
        assert "admin@okinacraft.com" in html
check("Admin Login Page renders Tabler UI & Okina Craft branding", t_admin_login_ui)

# 6. Auth with admin@okinacraft.com
cj_okina = http.cookiejar.CookieJar()
op_okina = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj_okina))

def t_auth_okina():
    res = op_okina.open(BASE + "/admin/login")
    html = res.read().decode('utf-8')
    csrf = re.search(r'name="([^"]*csrf[^"]*)"\s+value="([^"]+)"', html)
    csrf_name = csrf.group(1) if csrf else 'csrf_test_name'
    token = csrf.group(2) if csrf else ''
    data = urllib.parse.urlencode({
        csrf_name: token,
        'email': 'admin@okinacraft.com',
        'password': 'OkinaCraft@2026!'
    }).encode('utf-8')
    post_res = op_okina.open(urllib.request.Request(BASE + "/admin/login", data=data))
    assert "/admin" in post_res.geturl() and "/admin/login" not in post_res.geturl()
    body = post_res.read().decode('utf-8')
    assert "Dashboard" in body or "Store Dashboard" in body
    assert "OKINA CRAFT" in body
check("Login succeeds with admin@okinacraft.com / OkinaCraft@2026!", t_auth_okina)

# 7. Demo credentials rejected
cj_demo = http.cookiejar.CookieJar()
op_demo = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj_demo))

def t_auth_demo():
    res = op_demo.open(BASE + "/admin/login")
    html = res.read().decode('utf-8')
    csrf = re.search(r'name="([^"]*csrf[^"]*)"\s+value="([^"]+)"', html)
    csrf_name = csrf.group(1) if csrf else 'csrf_test_name'
    token = csrf.group(2) if csrf else ''
    data = urllib.parse.urlencode({
        csrf_name: token,
        'email': 'demo@insignia.com',
        'password': 'demo@123'
    }).encode('utf-8')
    post_res = op_demo.open(urllib.request.Request(BASE + "/admin/login", data=data))
    # Must fail and remain on login page
    assert "/admin/login" in post_res.geturl()
check("Deleted demo@insignia.com is rejected on login", t_auth_demo)

# 8. Admin Protected Views
def t_admin_views():
    endpoints = [
        ("/admin", "Dashboard"),
        ("/admin/dashboard", "Dashboard"),
        ("/admin/products", "Products"),
        ("/admin/products/new", "Add New Product"),
        ("/admin/collections", "Collections"),
        ("/admin/orders", "Orders"),
        ("/admin/messages", "Messages"),
        ("/admin/settings", "Settings"),
    ]
    for path, keyword in endpoints:
        res = op_okina.open(BASE + path)
        assert res.status == 200, f"{path} returned {res.status}"
        body = res.read().decode('utf-8')
        assert keyword in body, f"Keyword '{keyword}' not in {path}"
        assert "OKINA CRAFT" in body, f"Okina Craft brand missing from {path}"
check("All Tabler Admin Panel routes load with Okina Craft sidebar", t_admin_views)

# 9. Product Search in Admin
def t_admin_search():
    res = op_okina.open(BASE + "/admin/products?q=Oversized")
    assert res.status == 200
    body = res.read().decode('utf-8')
    assert "Oversized" in body
check("Admin Products search filter functions properly", t_admin_search)

# 10. Admin Logout
def t_admin_logout():
    res = op_okina.open(BASE + "/admin")
    html = res.read().decode('utf-8')
    csrf = re.search(r'name="([^"]*csrf[^"]*)"\s+value="([^"]+)"', html)
    csrf_name = csrf.group(1) if csrf else 'csrf_test_name'
    token = csrf.group(2) if csrf else ''
    data = urllib.parse.urlencode({csrf_name: token}).encode('utf-8')
    logout_res = op_okina.open(urllib.request.Request(BASE + "/admin/logout", data=data))
    assert "/admin/login" in logout_res.geturl()
    
    # Verify accessing /admin now redirects to login
    get_res = op_okina.open(BASE + "/admin")
    assert "/admin/login" in get_res.geturl()
check("Admin Logout revokes session and redirects to /admin/login", t_admin_logout)

print("--------------------------------------------------")
print(f"Results: {passed} passed, {failed} failed")
print("==================================================")
if failed > 0:
    sys.exit(1)
