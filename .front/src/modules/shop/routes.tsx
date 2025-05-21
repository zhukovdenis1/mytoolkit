//import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import {Outlet} from "react-router-dom";
import {ShopIndexPage} from "./pages/ShopIndexPage";
import {shopArticlesRoutes} from '@/modules/shopArticles/routes';
import {shopParsingRoutes} from '@/modules/shopParsing/routes';

export const shopRoutes: IRoute =
{
    path: "shop",
    element: <Outlet />,
    title: "Shop",
    children: [
        {
            name: 'shop',
            index: true,
            element: <ShopIndexPage /> ,
        },
        shopArticlesRoutes,
        shopParsingRoutes
    ]
};
