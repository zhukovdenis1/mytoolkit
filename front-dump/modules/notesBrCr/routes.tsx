/*export const NotesRoute = {
    index: '/notes/',
    category: {
        index: (categoryId: number) => `/notes/category/${categoryId}`,
        add: '/notes/category/add',
        edit: (categoryId: number) => `/notes/category/${categoryId}/edit`,
    }
};*/
import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import React from "react";
import {Outlet} from "react-router-dom";
import {Test} from "@/modules/notes/pages/Test.tsx";


export const notesRoutes: IRoute =
{
    path: "notes",
    element: <NotesProvider><Outlet /></NotesProvider>,
    title: "Notes",
    children: [
        {
            name: 'notes',
            index: true,
            element: <Test /> ,
        },
        {
            name: `notes.categories`,
            path: 'categories',
            title: 'categories',
            element: <p>Categories...</p>
        }
    ]
};
