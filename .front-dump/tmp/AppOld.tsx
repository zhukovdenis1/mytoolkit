import { RouterProvider, createBrowserRouter, createRoutesFromElements } from 'react-router-dom';
import { Route } from "react-router-dom";
import { AuthProvider } from '@/modules/auth/AuthProvider';
import { protectedRoutes, publicRoutes } from './router';
import Breadcrumbs from './components/Breadcrumbs';

import api from "@/modules/notes/services/noteApi";

import { Provider } from "react-redux";
//import { store } from "@/modules/notes/store/store"; // Импорт Redux store
//import { store } from "@/modules/notes2/store/store"; // Импорт Redux store
import { store } from "@/store/store"; // Импорт Redux store

import { RouteObject } from 'react-router-dom';
import React, {JSX} from "react";
import { convertRoutesToObject } from './utils/convertRoutes';
import {Layout} from "@/layouts/Layout.tsx"; // Путь к функции


// Создаём маршруты
// const router = createBrowserRouter(createRoutesFromElements(
//     <>
//         {protectedRoutes}
//         {publicRoutes}
//     </>
// ));

import { routesNew, IRoute } from "./routesnew2";
import {generateRoutes} from "@/utils/router.tsx";
import {RequireAuth} from "@/modules/auth/components/RequireAuth.tsx";
/*
const convertToRoutesOld = (routes: IRoute[]): React.ReactNode[] => {
    const generateRoutes = (routes: IRoute[]): React.ReactNode[] =>
        routes.map((route) => {
            const isIndex = route.path === "";
            return (
                <Route
                    key={route.name || route.path}
                    path={isIndex ? undefined : route.path}
                    index={isIndex}
                    element={route.component}
                >
                    {route.children && generateRoutes(route.children)}
                </Route>
            );
        });

    return generateRoutes(routes);
};

// Пример использования:
const routesOld = convertToRoutesOld(routesNew);

const router = createBrowserRouter(createRoutesFromElements(
    <>
        {routesOld}
    </>
));


function App() {
    return (
        // <Provider store={store}>
            <AuthProvider>
                <RouterProvider router={router} />
            </AuthProvider>
        // </Provider>
    );
}*/

import { useRoutes } from 'react-router-dom';

function App() {
    const element = useRoutes(routesNew);
    return  <>{element}</>;
}

export default App;
