//import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import {Outlet} from "react-router-dom";
import {ShopParsingListPage} from "./pages/ShopParsingListPage.tsx";
import {ShopParsingFormPage} from "./pages/ShopParsingFormPage.tsx";

export const shopParsingRoutes: IRoute =
{
    path: "parsing",
    element: <Outlet />,
    title: "Parsing",
    children: [
        {
            name: 'shop.parsing',
            index: true,
            element: <ShopParsingListPage /> ,
        },
        {
            name: "shop.parsing.add",
            path: "add",
            element:<ShopParsingFormPage />,
            title: "add"
        }
    ]
};
