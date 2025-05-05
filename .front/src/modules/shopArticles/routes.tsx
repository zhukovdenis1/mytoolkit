//import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import {Outlet} from "react-router-dom";
import {ShopArticlesListPage} from "./pages/ShopArticlesListPage";
import {ShopArticlesFormPage} from "./pages/ShopArticlesFormPage";
import React from "react";

export const shopArticlesRoutes: IRoute =
{
    path: "articles",
    element: <Outlet />,
    title: "Articles",
    children: [
        {
            name: 'shop.articles',
            index: true,
            element: <ShopArticlesListPage /> ,
        },
        {
            name: "shop.articles.add",
            path: "articles",
            element:<ShopArticlesFormPage />,
            title: "Add article"
        },
    ]
};
