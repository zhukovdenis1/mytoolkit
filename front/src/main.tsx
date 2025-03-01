// Entry point

import * as ReactDOMClient from "react-dom/client";
import '@/css/index.css';
import {createBrowserRouter, RouteObject, RouterProvider} from 'react-router-dom';
import {IRoute, routes} from "./routes";
import {AuthProvider} from '@/modules/auth/AuthProvider';
//import "prismjs/themes/prism-tomorrow.css";
import ErrorBoundary from '@/components/ErrorBoundary';

const rootElement = document.getElementById("root");

if (!rootElement) {
    throw new Error("Root element not found. Make sure there is a div with id 'root' in your index.html.");
}

const router = createBrowserRouter(transformRoutes(routes));

//import {route} from '@/utils/route';
//console.log(route('about.test', {id: 1}]));

ReactDOMClient.createRoot(rootElement).render(
    // <React.StrictMode>
    <ErrorBoundary>
        <AuthProvider>
            <RouterProvider router={router} />
        </AuthProvider>
    </ErrorBoundary>
    // </React.StrictMode>
);

function transformRoutes(routes: IRoute[]): RouteObject[] {
    return routes.map(({ path, element, children, index, ...rest }: IRoute) => {
        const handle = { ...rest };

        if (index === true) {
            return {
                index: true,
                element,
                children: children ? transformRoutes(children) : undefined,
                handle: Object.keys(handle).length > 0 ? handle : undefined,
            } as RouteObject;
        } else {
            return {
                path,
                element,
                children: children ? transformRoutes(children) : undefined,
                handle: Object.keys(handle).length > 0 ? handle : undefined,
            } as RouteObject;
        }
    });
}

