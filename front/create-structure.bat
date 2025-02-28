cd C:\OSPanel\home\mytoolkit.loc\next-5

:: Создание папок модулей
mkdir src\modules\auth
mkdir src\modules\auth\components
mkdir src\modules\auth\pages
mkdir src\modules\auth\api
mkdir src\modules\auth\hooks

mkdir src\modules\blog
mkdir src\modules\blog\components
mkdir src\modules\blog\pages
mkdir src\modules\blog\api
mkdir src\modules\blog\hooks

mkdir src\modules\events
mkdir src\modules\events\components
mkdir src\modules\events\pages
mkdir src\modules\events\api
mkdir src\modules\events\hooks

mkdir src\modules\notes
mkdir src\modules\notes\components
mkdir src\modules\notes\pages
mkdir src\modules\notes\api
mkdir src\modules\notes\hooks

:: Общие папки
mkdir src\shared\components
mkdir src\shared\hooks
mkdir src\shared\utils
mkdir src\shared\types

:: Конфигурации
mkdir src\config
mkdir public

:: Создание файлов (примерные)
echo // Entry point > src\main.tsx
echo // Global styles > src\index.css
echo // API config > src\shared\utils\api.ts
echo // Auth provider > src\shared\components\AuthProvider.tsx

:: Файлы для auth модуля
echo // LoginPage > src\modules\auth\pages\LoginPage.tsx
echo // SignupPage > src\modules\auth\pages\SignupPage.tsx
echo // Auth hooks > src\modules\auth\hooks\useAuth.ts
echo // Auth API > src\modules\auth\api\authApi.ts

:: Файлы для blog модуля
echo // BlogPage > src\modules\blog\pages\BlogPage.tsx
echo // SinglePost > src\modules\blog\pages\SinglePost.tsx
echo // Blog hooks > src\modules\blog\hooks\useBlog.ts
echo // Blog API > src\modules\blog\api\blogApi.ts

:: Файлы для events модуля
echo // EventsPage > src\modules\events\pages\EventsPage.tsx
echo // SingleEvent > src\modules\events\pages\SingleEvent.tsx
echo // Events hooks > src\modules\events\hooks\useEvents.ts
echo // Events API > src\modules\events\api\eventsApi.ts

:: Файлы для notes модуля
echo // NotesPage > src\modules\notes\pages\NotesPage.tsx
echo // SingleNote > src\modules\notes\pages\SingleNote.tsx
echo // Notes hooks > src\modules\notes\hooks\useNotes.ts
echo // Notes API > src\modules\notes\api\notesApi.ts

:: Файлы для shared
echo // AuthContext > src\shared\hooks\useAuthContext.ts
echo // Reusable component > src\shared\components\Button.tsx
echo // Shared types > src\shared\types\index.ts
