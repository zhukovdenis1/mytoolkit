//import authRoutes from './modules/auth/routes';
//import blogRoutes from '../modules/blog/routes';

import { Route } from 'react-router-dom';

import { HomePage } from './pages/Home';
import { About } from './pages/About.tsx';
import { LoginPage } from './modules/auth/pages/LoginPage'
import ErrorPage from './pages/Error';
import { NotFoundPage } from './pages/NotFound'

import { Layout } from './layouts/Layout';
import { LoginLayout} from "@/layouts/LoginLayout";
import { RequireAuth } from './modules/auth/components/RequireAuth';
//import NoteRoutes from './modules/notes/pages/Routes'
import NoteRoutes from "./modules/notes/router";
import Note2Routes from './modules/notes2/index'
import {NotesProvider} from "@/modules/notes/NotesProvider";

//import CategoryListPage from "./modules/notes/pages/CategoryListPage.tsx";
import React from "react";
import { convertRoutesToObject } from './utils/convertRoutes';



export const routes = [
    {
        path: "/",
        element: <HomePage />,
        label: "Home",
        name: 'home',
        children: [
            {
                path: "about",
                element: <About />,
                label: "About",
                name: 'about',
                children: [
                    { path: "contacts", element: <p>Our contact...</p>, label: "Contacts" },
                    { path: "team", element: <p>Our team...</p>, label: "Team" },
                ],
            },
            {
                path: "notes/*",
                element: <NotesProvider><NoteRoutes /></NotesProvider>,
                label: "Notes",
            },
        ],
    },
    {
        path: "*",
        element: <NotFoundPage />,
        label: "Not Found",
    },
];
/*
export const protectedRoutes = (
    <Route path="/" element={<RequireAuth><Layout allRoutes={routes} /></RequireAuth>}>
        {routes.map((route, index) => (
            <Route key={index} path={route.path} element={route.element}>
                {route.children && route.children.map((child, childIndex) => (
                    <Route key={childIndex} path={child.path} element={child.element} />
                ))}
            </Route>
        ))}
    </Route>
);*/


// Защищённые маршруты

export const protectedRoutes = (
    <Route path="/" element={<RequireAuth><BreadcrumbsProvider><Layout allRoutes={routes} /></BreadcrumbsProvider></RequireAuth>}>
        <Route index element={<HomePage />} />
        <Route path="about" element={<About />} >
            <Route path="contacts" element={<p>Our contact...</p>} />
            <Route path="team" element={<p>Our team...</p>} />
        </Route>

        <Route path="notes/*" element={<NotesProvider><NoteRoutes /></NotesProvider>} errorElement={<ErrorPage />} />

        <Route path="notes2/*" element={<Note2Routes />} errorElement={<ErrorPage />} />
        <Route path="*" element={<NotFoundPage />} />

    </Route>
);



// Публичные маршруты
export const publicRoutes = (
    <Route path="/login" element={<LoginLayout />}>
        <Route index element={<LoginPage />} />
    </Route>
);

/*
import { useRoutes } from 'react-router-dom';

const routes = [
    {
        path: "/",
        element: <RequireAuth><Layout allRoutes={routes} /></RequireAuth>,
        children: [
            {
                index: true,
                element: <HomePage />,
            },
            {
                path: "about",
                element: <About />,
                children: [
                    {
                        path: "contacts",
                        element: <p>Our contact...</p>,
                    },
                    {
                        path: "team",
                        element: <p>Our team...</p>,
                    },
                ],
            },
            {
                path: "notes/*",
                element: <NotesProvider><NoteRoutes /></NotesProvider>,
                errorElement: <ErrorPage />,
            },
            {
                path: "notes2/*",
                element: <Note2Routes />,
                errorElement: <ErrorPage />,
            },
            {
                path: "*",
                element: <NotFoundPage />,
            },
        ],
    },
];

function App() {
    const element = useRoutes(routes);
    return <div>{element}</div>;
}

export default App;*/


/*
interface RouteObject {
  path?: string;                // Путь маршрута, например, "/about"
  index?: boolean;              // Если true, этот маршрут будет маршрутом по умолчанию для родительского маршрута
  element?: React.ReactNode;     // Компонент, который будет рендериться для этого маршрута
  children?: RouteObject[];     // Массив дочерних маршрутов
  loader?: RouteLoaderFunction; // Функция загрузчика данных (например, для серверных данных)
  action?: RouteActionFunction; // Функция действия, например, для обработки форм
  errorElement?: React.ReactNode; // Компонент для отображения в случае ошибки маршрута
  meta?: Record<string, unknown>; // Дополнительные метаданные маршрута
  caseSensitive?: boolean;      // Если true, маршрут будет чувствителен к регистру
  hasErrorBoundary?: boolean;   // Если true, этот маршрут будет использовать собственный компонент для обработки ошибок
}
* */
