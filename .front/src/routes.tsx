import React from "react";
import { NoteTreePage } from '@/modules/notes/pages/NoteTreePage';
import { About } from './pages/About.tsx';
import { Contacts } from './pages/Contacts';
import {Layout} from "@/layouts/Layout.tsx";
import {RequireAuth} from "@/modules/auth/components/RequireAuth.tsx";
import {LoginLayout} from "@/layouts/LoginLayout.tsx";
import {LoginPage} from "@/modules/auth/pages/LoginPage.tsx";
import {notesRoutes} from '@/modules/notes/routes';
import { BreadcrumbsProvider } from "@/components/BreadCrumbs";
import {Outlet} from "react-router-dom";

import {DemoPage} from "@/pages/Demo/DemoPage.tsx";
import {DemoTplPage} from "@/pages/Demo/DemoTplPage.tsx";
//import {DemoTreeCategoriesPage} from "@/pages/Demo/DemoTreeCategoriesPage.tsx";
import {DemoNoteCategoryListPage} from "@/pages/Demo/DemoNoteCategoryListPage";
import {DemoGoogleDrivePage} from "@/pages/Demo/DemoGoogleDrivePage";
import { GoogleOAuthProvider} from '@react-oauth/google';
import YandexDiskUploader from "@/pages/Demo/YandexDiskUploader";
import YandexDiskImage from "@/pages/Demo/YandexDiskImage";

export interface IRoute {
    name?: string;
    title?: string;
    path?: string;
    index?: boolean;
    element?: React.ReactNode;
    children?: IRoute[];
}

export const routes: IRoute[] = [
    {
        path: "/",
        element: (
            <RequireAuth>
                <BreadcrumbsProvider>
                    <Layout />
                </BreadcrumbsProvider>
            </RequireAuth>
        ),
        title: "Home",
        children: [
            {
                name: "home",
                index: true,
                element: <NoteTreePage />,
            },
            {
                name: "about",
                path: "about",
                element: <About />,
                title: "О нас",
                children: [
                    {
                        name: "about.contacts",
                        path: "contacts",
                        element: <Contacts />,
                        title: "Контакты",
                    },
                    {
                        name: "about.test",
                        path: "t/e/s/:id",
                        element: <p>Test...</p>,
                        title: "Test"
                    },
                ],
            },
            {
                path: "demo",
                element: <Outlet />,
                title: "Demo",
                children: [
                    {
                        name: 'demo',
                        index: true,
                        element: <DemoPage /> ,
                    },
                    // {
                    //     name: "demo.tree",
                    //     path: "tree",
                    //     element: <DemoTreeCategoriesPage />,
                    //     title: "Tree с подсветкой найденного",
                    // },
                    {
                        name: "demo.note_category_list",
                        path: "note_category_list",
                        element: <DemoNoteCategoryListPage />,
                        title: "DemoNoteCategoryListPage",
                    },
                    {
                        name: "demo.tpl",
                        path: "tpl",
                        element: <DemoTplPage />,
                        title: "Шаблон",
                    },
                    {
                        name: "google",
                        path: "google",
                        element: <GoogleOAuthProvider clientId="YOUR_CLIENT_ID"><DemoGoogleDrivePage /></GoogleOAuthProvider>,
                        title: "Google Upload",
                    },
                    {
                        name: "yandex",
                        path: "yandex",
                        element: <YandexDiskUploader />,
                        title: "Yandex Upload",
                    },
                    {
                        name: "ya",
                        path: "ya",
                        element: <YandexDiskImage />,
                        title: "Yandex Upload",
                    }
                ],
            },
            notesRoutes,
        ],
    },
    {
        path: "/login",
        element: <LoginLayout />,
        children: [
            {
                name: "login",
                index: true,
                element: <LoginPage />,
                title: "О Login",
            },
        ],
    },
];
