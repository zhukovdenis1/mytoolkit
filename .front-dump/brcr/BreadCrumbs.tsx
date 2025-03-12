import React from "react";
import { Link } from "react-router-dom";
import { useBreadcrumbsContext } from "@/components/BreadCrumbsContext";

const Breadcrumbs: React.FC = () => {
    const { breadcrumbs } = useBreadcrumbsContext();

    return (
        <nav className="brcr">
            {breadcrumbs.map((crumb, index) => (
                <span key={`${crumb.path}-${index}`}>
                    <Link to={crumb.path}>{crumb.title}</Link>
                    {index < breadcrumbs.length - 1 && " > "}
                </span>
            ))}
        </nav>
    );
};

export default Breadcrumbs;
