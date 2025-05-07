//import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import {Outlet} from "react-router-dom";
import {ShopArticlesListPage} from "./pages/ShopArticlesListPage";
import {ShopArticlesFormPage} from "./pages/ShopArticlesFormPage";

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
            path: "add",
            element:<ShopArticlesFormPage />,
            title: "add"
        },
        {
            name: "shop.articles.edit",
            path: ":article_id/edit",
            element:<ShopArticlesFormPage />,
            title: "edit"
        }
    ]
};
