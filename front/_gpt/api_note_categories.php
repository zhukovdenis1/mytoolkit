Нужно написать на react+ts модуль для управления пока только категориями заметок
Важно. Генерируй всё в этом окне, не надо открывать дополнительные. Не останавливайся на пол пути
и не спрашивай нужно ли продолжить. Ответ - да, нужно. Давай от и до в точности по ТЗ.

папка модуля: /src/modules/notes

├── modules/
│   ├── notes/
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── pages/
│   │   ├── services/
│   │   ├── store/
│   │   └── types/

uri модуля: /notes/...

---

Такой Апи, где {{url}} = https://mytoolkit.loc

POST {{url}}/api/note/categories
Accept: application/json
Authorization: Bearer {{access_token}}
Content-Type: application/json

{
"name": "video",
"parent_id": null
}

Ответ:

{
    "noteCategory": {
        "id": 2,
        "parentId": null,
        "name": "video"
    }
}

---------------
PUT {{url}}/api/note/categories/4
Accept: application/json
Authorization: Bearer {{access_token}}
Content-Type: application/json

{
    "name": "js"
    "parent_id": 1
}

Ответ:

{
    "noteCategory": {
        "id": 4,
        "parentId": 1,
        "name": "js"
    },
    "success": true
}
----------------
DELETE {{url}}/api/note/categories/6
Accept: application/json
Authorization: Bearer {{access_token}}
Content-Type: application/json

Ответ:
{
"success": true
}
-------------------
GET {{url}}/api/note/categories
Accept: application/json
Authorization: Bearer {{access_token}}
Content-Type: application/json

{
"data": [
        {
            "id": 1,
            "parentId": null,
            "name": "dev"
        },
        {
            "id": 2,
            "parentId": null,
            "name": "video"
        },
        {
            "id": 3,
            "parentId": 1,
            "name": "php"
        },
        {
            "id": 4,
            "parentId": 1,
            "name": "js"
        },
        {
            "id": 5,
            "parentId": 2,
            "name": "php"
        }
    ]
}

Апи может возращать ошибки в таком формате:

{
    "errors": {
        "name": [
        "The name field is required."
        ]
    }
}

"name" - это название поле, которое не прошло валидацию. В формах нужно будет такое поле выделять классом "error-field"
дополнительно к отображению самого текста ошибки

----

На главной странице модуля будет форма поиска, кнопка edit_mode(on/off), 2 таба: список | поиск

Изначально активнен таб: спиок, кнопка edit_mode - off

Отображается список кактегорий (полученный из GET {{url}}/api/note/categories/ в виде дерева, напирмер:

* dev
  * php
  * js
* video
  * php
  * разное
* big_example
  * e1
  * e2
  * e3
  * e4
  * e5
  * [...]

изначально этот спиок раскрыт только до второго уровня. Если в уровне более 5 элементов, то появляется [...] при клике
на [...] - отображаются остальные пункты. (на первом уровне полностью все элементы отображаются, по 5 - начиная со второго уровня)
каждое название пункта - это ссылка при клике на которую происходит сворачиваниеи разворачивание подкатегорий

если нажать кнопку edit_mode (у нее появится класс="on", по умолчанию "off") то около каждого пункта меню появится 3 ссылки (edit, del, [drag and drop])
а в конце каждого списка - input поле для добавления новго элемента в родильский раздел:
в режиме off эти ссылки и input поле скрываются

* dev [edit] [del] [drag and drop]
    * php [edit] [del] [drag and drop]
    * js [edit] [del] [drag and drop]
    * [input]
* video [edit] [del] [drag and drop]
    * php [edit] [del] [drag and drop]
    * разное [edit] [del] [drag and drop]
    * [input]
* [input]

[input] - поле ввода. если ввести название и нажать enter - то переход на форму добавления
del - ссылка на удаление: DELETE {{url}}/api/note/categories/[int], ели от api прохидт "success": true, то удаляется, если false - alert, с ошибкой
edit - ссылка на редактирование, input - добавление категории
Каждый пункт этого списка можно через drag and drop перекинуть  другой раздел если держать зажатой эту кнопку [drag and drop]

добавление и редактирование - это переход на соотвествующую форму. В форме должны быть поле name и parent_id
и 3 кнопки (сохранить, "сохранить и вернуться", "отменить")

После возврата ("сохранить и вернуться","отменить") список раскрыт на соответсвующем добавленном/отредактированном пункте и скрол страницы к этому пункту.
если нажата "сохранить и вернуться" и в результате от api success: true, то на 2 сек появляется надпись что всё ок, и проихсодит переход
если нажата "сохранить и вернуться" и в результате от api success: false или errors: {...}, то вверху форму уведомление что не удалось сохранить и перехода не происходит
если нажата "отменить" то просто переход
если нажата "сохранить" то вверху над формой уведомлени ок или error, перехода не происходит

---

Если что-то вводить в поле поиска, то становится активным таб "поиск" (поиск производится на стороне клиента по данным GET {{url}}/api/note/categories/)

например мы ввели  "ph"

в теле этого таба должен быть список

* dev/php [list]
* video/php [list]

[list] - это ссылки на соответсвующий пункт в табе "список" (при клике - такой же переход, как после возврата с редактирования/добавления)
Если при этом нажата кнопка edit_mode, то так же появляются ссылки [edit] [del] как и в табе "список"

---

Делай рабивку по файлам, я хочу просто копипаст кода сделать
Можешь использовать ReduxToolkit, если это необходимо. Но если используель какие-то библиотеки, то
явно об этом напиши и сообщи как их установить
Сделай полную реализацию в точности с тз с реализацией компонентов, таких как DragAndDropList . Важно каждое слово.
Давай без таких вот комментариев:       {/* Реализация Drag-and-Drop списка */}
<p>Drag-and-Drop функциональность пока не реализована</p> Реализуй пожалуйста весь функционал (и не забудь про кнопку edit_mode).
Для реализации drag-and-drop используй библиотеку dnd-kit

Если подключаешь какие-то библиотеки, то явно напиши об этом и как их установить

Вот мой package.json:

{
"name": "next-5",
"private": true,
"version": "0.0.0",
"type": "module",
"scripts": {
"dev": "vite",
"build": "tsc -b && vite build",
"lint": "eslint .",
"preview": "vite preview"
},
"dependencies": {
"axios": "^1.7.9",
"js-cookie": "^3.0.5",
"react": "^19.0.0",
"react-dom": "^19.0.0",
"react-router-dom": "^7.1.1"
},
"devDependencies": {
"@eslint/js": "^9.17.0",
"@types/js-cookie": "^3.0.6",
"@types/react": "^19.0.2",
"@types/react-dom": "^19.0.2",
"@vitejs/plugin-react": "^4.3.4",
"eslint": "^9.17.0",
"eslint-plugin-react-hooks": "^5.0.0",
"eslint-plugin-react-refresh": "^0.4.16",
"globals": "^15.14.0",
"typescript": "~5.6.2",
"typescript-eslint": "^8.18.2",
"vite": "^6.0.5"
}
}

И еще.. Это закрытый авторизацией модуль. У меня есть файл /src/services/api.tsx. Вот его содержимое:

import axios, { AxiosError } from "axios";
import Cookies from "js-cookie";

const api = axios.create({
baseURL: "https://mytoolkit.loc/api",
headers: {
Accept: "application/json",
"Content-Type": "application/json",
},
});

api.interceptors.request.use((config) => {
const token = localStorage.getItem("access_token");
if (token) {
config.headers.Authorization = `Bearer ${token}`;
}
return config;
});

api.interceptors.response.use(
(response) => response,
async (error: AxiosError) => {
const originalRequest = error.config!;
if (error.response?.status === 401 && !originalRequest._retry) {
originalRequest._retry = true;
try {
const refreshToken = Cookies.get("refresh_token");
if (!refreshToken) throw new Error("No refresh token");

/*const { data } = await axios.post<{ access_token: string; refresh_token: string }>(
"https://mytoolkit.loc/api/auth/refresh",
{ refresh_token: refreshToken }
);*/

const { data } = await api.post<{ access_token: string; refresh_token: string }>(
"/auth/refresh",
{ refresh_token: refreshToken }
);

localStorage.setItem("access_token", data.access_token);
Cookies.set("refresh_token", data.refresh_token);

return api(originalRequest); // Повторяем запрос
} catch (refreshError) {
console.error("Token refresh failed:", refreshError);
throw refreshError;
}
}
return Promise.reject(error);
}
);

export default api;

