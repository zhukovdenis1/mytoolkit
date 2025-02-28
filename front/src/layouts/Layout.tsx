// components/Layout.tsx
import React, { useContext } from "react";
import {Link, Outlet} from "react-router-dom";
import { AuthContext } from "@/modules/auth/AuthProvider";
import {Breadcrumbs} from "@/components/BreadCrumbs";
import {route} from "api";
import {Button} from "ui";

//export const Layout: React.FC<{ allRoutes: {} }> = ({ allRoutes }) => {
export const Layout: React.FC = () => {
    const authContext = useContext(AuthContext);

    return (

        <div className="wrapper">
            <div className="center-wrap">
                <header>
                    <nav>
                        <Link to="/">Home</Link> |
                        <Link to="/about">About</Link> |{" "} |
                        <Link to={route('notes')}>Notes</Link> |
                        <Link to={route('notes.categories')}>Categories</Link> |
                        <Link to="/demo">Demos</Link> |
                    </nav>
                    {authContext?.user ? (
                        <div>
                            {/*Hello, {authContext.user.email}*/}
                            <Button type="link" onClick={() => authContext.signout(() => window.location.reload())}>
                                Logout
                            </Button>
                        </div>
                    ) : <Link to="/login">Login</Link>}
                </header>
                <main>
                     <Breadcrumbs />
                    <Outlet />
                </main>
            </div>
        </div>

    );
};
