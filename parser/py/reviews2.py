import json
import time
import random
import os
from urllib.parse import unquote
from playwright.sync_api import sync_playwright

COOKIES_FILE = "aliexpress_cookies.json"
MAX_RETRIES = 3

def save_cookies(context):
    cookies = context.cookies()
    with open(COOKIES_FILE, 'w') as f:
        json.dump(cookies, f)
    print("✓ Куки сохранены")

def load_cookies(context):
    if os.path.exists(COOKIES_FILE):
        with open(COOKIES_FILE, 'r') as f:
            cookies = json.load(f)
            context.add_cookies(cookies)
        print("✓ Куки загружены")
        return True
    return False

def is_captcha_url(url):
    """Точная проверка URL капчи (только основной URL, не параметры)"""
    return "/_____tmd_____/punish" in url

def wait_for_reviews_page(page, timeout=120000):
    """Ожидание загрузки корректной страницы отзывов"""
    print("⏳ Ожидаем загрузки страницы отзывов...")
    start_time = time.time()

    while True:
        current_url = page.url
        if time.time() - start_time > timeout:
            raise TimeoutError("Превышено время ожидания загрузки страницы")

        # Проверяем что это URL отзывов И НЕ URL капчи
        if "/item/1005008081521104/reviews" in current_url and not is_captcha_url(current_url):
            try:
                # Дополнительная проверка видимости контента
                page.wait_for_selector("div.reviews-content", timeout=10000)
                print("✓ Страница отзывов загружена")
                return True
            except:
                print("× Контент отзывов не найден, продолжаем ожидание...")

        time.sleep(2)

def parse_reviews():
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
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            viewport={"width": 1920, "height": 1080},
            locale="ru-RU",
            timezone_id="Europe/Moscow"
        )

        # Загрузка куки
        load_cookies(context)
        page = context.new_page()

        # Устанавливаем Referer как будто перешли со страницы товара
        page.set_extra_http_headers({
            "Referer": "https://aliexpress.ru/item/1005008081521104.html"
        })

        # Основной URL страницы отзывов
        #reviews_url = "https://aliexpress.ru/item/1005008081521104/reviews?sku_id=12000048377048266"
        reviews_url = "https://aliexpress.ru/item/1005008081521104/reviews"

        try:
            # Прямой переход на страницу отзывов
            print("🌐 Загружаем страницу отзывов...")
            page.goto(reviews_url, timeout=60000)

            # Обработка капчи (если появилась)
            if is_captcha_url(page.url):
                print("\n=== НЕОБХОДИМО РЕШИТЬ КАПЧУ ===")
                print("1. Решите капчу в открывшемся браузере")
                print("2. Дождитесь полной загрузки страницы отзывов")
                print("3. Скрипт продолжит работу автоматически")
                print("===============================\n")

                # Ждем когда URL изменится на корректный
                wait_for_reviews_page(page)
                save_cookies(context)

            # Настройка перехвата AJAX-запросов
            captured_pages = set()

            def handle_response(response):
                if "aer-jsonapi/review" in response.url and response.request.method == "POST":
                    try:
                        # Для POST-запросов
                        post_data = response.request.post_data
                        if post_data:
                            data = json.loads(post_data)
                            page_num = data.get("pagination", {}).get("pageNum", 0)

                            if 1 <= page_num <= 5 and page_num not in captured_pages:
                                # Сохраняем и сам запрос, и ответ
                                result = {
                                    "request": data,
                                    "response": response.json()
                                }

                                filename = f"reviews_page_{page_num}.json"
                                with open(filename, 'w', encoding='utf-8') as f:
                                    json.dump(result, f, ensure_ascii=False, indent=2)

                                captured_pages.add(page_num)
                                print(f"✓ Сохранена страница {page_num}")
                    except Exception as e:
                        print(f"× Ошибка обработки: {e}")

            page.on("response", handle_response)

            # Прокрутка для загрузки отзывов
            print("🔄 Начинаем прокрутку...")
            for attempt in range(1, 31):  # Максимум 30 попыток
                if len(captured_pages) >= 5:
                    break

                # Проверяем не попали ли снова на капчу
                if is_captcha_url(page.url):
                    print("Обнаружена капча, требуется решение")
                    wait_for_reviews_page(page)
                    save_cookies(context)
                    continue

                # Плавная прокрутка
                scroll_amount = random.randint(800, 1200)
                page.evaluate(f"""
                    window.scrollBy({{
                        top: {scroll_amount},
                        behavior: 'smooth'
                    }});
                """)

                # Случайные действия мыши
                if random.random() > 0.5:
                    page.mouse.move(
                        random.randint(100, 500),
                        random.randint(100, 500)
                    )

                # Случайная задержка
                delay = random.uniform(2.0, 4.0)
                time.sleep(delay)
                print(f"↻ Прокрутка {attempt}, собрано: {len(captured_pages)}/5")

            print(f"✅ Готово! Собрано {len(captured_pages)} страниц отзывов")

        except Exception as e:
            print(f"× Критическая ошибка: {e}")
        finally:
            save_cookies(context)
            time.sleep(2)
            browser.close()

if __name__ == "__main__":
    parse_reviews()
