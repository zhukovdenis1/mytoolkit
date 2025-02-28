import {NotesProvider} from "@/modules/notes/NotesProvider.tsx";

import {IRoute} from "@/routes";
import React from "react";
import {Outlet} from "react-router-dom";
import {NoteSearchPage} from "./pages/NoteSearchPage";
import {NoteCategoryListPage} from "./pages/categories/NoteCategoryListPage";
import {NoteFormPage} from "./pages/NoteFormPage.tsx";
import {NoteViewPage} from "./pages/NoteViewPage.tsx";

export const notesRoutes: IRoute =
    {
        path: "notes",
        element: <Outlet />,
        title: "Заметки",
        children: [
            {
                name: 'notes',
                index: true,
                element: <NoteSearchPage /> ,
            },
            {
                name: 'notes.view',
                path: ':note_id',
                title: 'Note',
                element: <NoteViewPage />
            },
            {
                name: 'notes.add',
                path: 'add',
                title: 'Add note',
                element: <NoteFormPage />
            },
            {
                name: 'notes.edit',
                path: ':note_id/edit',
                title: 'Edit Note',
                element: <NoteFormPage />
            },
            {
                path: "categories",
                element: <Outlet />,
                title: "Categories",
                children: [
                    {
                        name: 'notes.categories',
                        index: true,
                        element: <NoteCategoryListPage /> ,
                    },
                    // {
                    //     name: 'notes.categories.edit',
                    //     path: ':category_id/edit',
                    //     title: 'Редактировать раздел',
                    //     element: <NoteCategoryEditPage />,
                    // }
                ],
            },


        ]
    };
