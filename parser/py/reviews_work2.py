import json
import time
import random
import os
import requests
from urllib.parse import unquote
from playwright.sync_api import sync_playwright
import urllib3

# Конфигурация
class Config:
    get_url = 'https://deshevyi.loc/api/shop/get-product-for-reviews-parse'
    set_url = 'https://deshevyi.loc/api/shop/set-parsed-reviews'
    max_page = 35
    scroll_delay_min = 1  # сек
    scroll_delay_max = 3  # сек
    delay_min = 10  # сек
    delay_max = 60  # сек
    cookies_file = "aliexpress_cookies.json"
    user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    verify_ssl = False

config = Config()

def save_cookies(context):
    cookies = context.cookies()
    with open(config.cookies_file, 'w') as f:
        json.dump(cookies, f)
    print("✓ Куки сохранены")

def load_cookies(context):
    if os.path.exists(config.cookies_file):
        with open(config.cookies_file, 'r') as f:
            cookies = json.load(f)
            context.add_cookies(cookies)
        print("✓ Куки загружены")
        return True
    return False

def is_captcha_url(url):
    return "/_____tmd_____/punish" in url

def wait_for_reviews_page(page, timeout=120000):
    print("⏳ Ожидаем загрузки страницы отзывов...")
    start_time = time.time()

    while True:
        current_url = page.url
        if time.time() - start_time > timeout:
            raise TimeoutError("Превышено время ожидания загрузки страницы")

        if "/reviews" in current_url and not is_captcha_url(current_url):
            try:
                page.wait_for_selector("div.reviews-content", timeout=10000)
                print("✓ Страница отзывов загружена")
                return True
            except:
                print("× Контент отзывов не найден, продолжаем ожидание...")

        time.sleep(2)

def get_product_for_parse():
    print(f"\n🔄 Делаем GET запрос к {config.get_url}")
    try:
        response = requests.get(config.get_url, verify=config.verify_ssl)
        print(f"✓ Получен ответ: {response.text}")

        data = response.json()
        if not data.get('data'):
            print("× Нет данных для парсинга")
            return None

        product_data = data['data']
        return {
            'product_id': product_data['product_id'],
            'url': product_data['url'],
            'referer': product_data['referer'],
            'id_ae': product_data['url'].split('/item/')[1].split('/')[0]
        }
    except Exception as e:
        print(f"× Ошибка при запросе: {e}")
        return None

def send_parsed_reviews(product_info, page_num, page_size, response_data):
    payload = {
        'page': page_num,
        'limit': page_size,
        'product_id': product_info['product_id'],
        'id_ae': product_info['id_ae'],
        'json': response_data
    }

    print(f"\n📤 Отправляем данные страницы {page_num} на сервер")
    try:
        response = requests.post(config.set_url, json=payload, verify=config.verify_ssl)
        print(f"✓ Ответ сервера (статус {response.status_code}): {response.text}")
        return True
    except Exception as e:
        print(f"× Ошибка при отправке данных: {e}")
        return False

def parse_reviews_page(product_info):
    with sync_playwright() as p:
        browser = p.chromium.launch(
            headless=False,
            channel="chrome",
            args=[
                "--start-maximized",
                "--disable-blink-features=AutomationControlled",
                "--disable-infobars",
                "--lang=ru-RU"
            ]
        )

        context = browser.new_context(
            user_agent=config.user_agent,
            viewport={"width": 1920, "height": 1080},
            locale="ru-RU",
            timezone_id="Europe/Moscow"
        )

        load_cookies(context)
        page = context.new_page()

        # Устанавливаем Referer из полученных данных
        page.set_extra_http_headers({
            "Referer": product_info['referer']
        })

        try:
            print(f"🌐 Загружаем страницу отзывов: {product_info['url']}")
            page.goto(product_info['url'], timeout=60000)

            if is_captcha_url(page.url):
                print("\n=== НЕОБХОДИМО РЕШИТЬ КАПЧУ ===")
                print("1. Решите капчу в браузере")
                print("2. Дождитесь полной загрузки страницы отзывов")
                print("3. Скрипт продолжит работу автоматически")
                print("===============================\n")

                wait_for_reviews_page(page)
                save_cookies(context)

            captured_pages = set()

            def handle_response(response):
                if "aer-jsonapi/review" in response.url and response.request.method == "POST":
                    try:
                        post_data = json.loads(response.request.post_data)
                        page_num = post_data.get("pagination", {}).get("pageNum", 0)
                        page_size = post_data.get("pagination", {}).get("pageSize", 10)
                        product_key = post_data.get("productKey", {}).get("id", "")

                        if 1 <= page_num <= config.max_page and page_num not in captured_pages:
                            response_data = response.json()
                            if send_parsed_reviews(product_info, page_num, page_size, response_data):
                                captured_pages.add(page_num)
                                print(f"✓ Страница {page_num} обработана")
                    except Exception as e:
                        print(f"× Ошибка обработки: {e}")

            page.on("response", handle_response)

            print("🔄 Начинаем прокрутку...")
            for attempt in range(1, 31):
                if len(captured_pages) >= config.max_page:
                    break

                if is_captcha_url(page.url):
                    print("Обнаружена капча, требуется решение")
                    wait_for_reviews_page(page)
                    save_cookies(context)
                    continue

                scroll_amount = random.randint(800, 1200)
                page.evaluate(f"""
                    window.scrollBy({{
                        top: {scroll_amount},
                        behavior: 'smooth'
                    }});
                """)

                if random.random() > 0.5:
                    page.mouse.move(
                        random.randint(100, 500),
                        random.randint(100, 500)
                    )

                delay = random.uniform(config.scroll_delay_min, config.scroll_delay_max)
                time.sleep(delay)
                print(f"↻ Прокрутка {attempt}, собрано: {len(captured_pages)}/{config.max_page}")

            print(f"✅ Готово! Собрано {len(captured_pages)} страниц отзывов")

        except Exception as e:
            print(f"× Критическая ошибка: {e}")
        finally:
            save_cookies(context)
            browser.close()

def main():
    while True:
        product_info = get_product_for_parse()
        if not product_info:
            delay = random.uniform(config.delay_min, config.delay_max)
            print(f"⏳ Нет данных для парсинга. Ждем {delay:.1f} сек...")
            time.sleep(delay)
            continue

        parse_reviews_page(product_info)

        delay = random.uniform(config.delay_min, config.delay_max)
        print(f"⏳ Завершено. Ждем {delay:.1f} сек перед следующим запросом...")
        time.sleep(delay)

if __name__ == "__main__":
    main()
