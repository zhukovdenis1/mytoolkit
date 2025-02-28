import React from "react";
import {route} from "@/utils/route.ts";

import {addBreadCrumbs} from "@/components/BreadcrumbsContext";
import {Link} from "react-router-dom";



export const Test: React.FC = () => {
    /*const {
        addBreadcrumb,
        removeLastBreadcrumb,
        clearBreadcrumbs,
        resetBreadCrumbs,
    } = useBreadcrumbsContext();*/

    /*useChangeBreadCrumbs()(() => {
        addBreadcrumb({ title: "Home1", path: "/asdf" })
        addBreadcrumb({ title: "Home2", path: "/asdf" })
        removeLastBreadcrumb();
        removeLastBreadcrumb();
        clearBreadcrumbs();
        resetBreadCrumbs();
        addBreadcrumb({ title: "Home1", path: "/asdf" })
    });*/

    addBreadCrumbs([{ title: "Home1", path: "/asdf" }, { title: "Home2", path: "/asdf" }])


    return (
        <div>
            <p>Notes...</p><Link to={route('notes.categories')}>Categories</Link>
            {/*<button onClick={() => addBreadcrumb({ title: "Home3", path: "/asdf" })}>add brcr</button>*/}
            {/*    Add Breadcrumb*/}
            {/*</button>*/}
            {/*<button onClick={removeLastBreadcrumb}>Remove Last</button>*/}
            {/*<button onClick={clearBreadcrumbs}>Clear All</button>*/}
            {/*<button onClick={resetToDefault}>Reset to Default</button>*/}
        </div>

    )
}
