import React, {useState, useEffect} from "react";
import {Link} from "react-router-dom";

import {route} from "@/utils/route";
import {api} from "@/services/api";
//import {useRequest} from "@/utils/useRequest"

export const DemoNoteCategoryListPage: React.FC = () => {
    //const request = useRequest();
    //const parentId = request.query('parent_id');

    const [categoryTree, setCategoriesTree] = useState<any[]>([])

    useEffect(() => {
        const fetchCategories = async () => {

            try {
                const response = await api.request("notes.categories.tree");
                setCategoriesTree(response.data.data || []);
                //navigate(route('notes.search', {search: searchString}));
            } catch (err) {

            } finally {
                //setLoading(false);
            }
        };
        fetchCategories();
    }, [/*parentId*/]);

    const renderTree = (tree: any[]) => {
        return (
            <ul>
                {tree.map((node) => (
                    <li key={node.value}>
                        <span>
                            {node.title}
                            &nbsp;&nbsp;
                            <Link to={route('notes.categories.edit', {category_id: node.value})}>edit</Link>
                        </span>
                        {node.children && node.children.length > 0 && renderTree(node.children)} {/* Рекурсивно рендерим дочерние элементы */}
                    </li>
                ))}
            </ul>
        );
    };


    return (
        <div>
            {renderTree(categoryTree)}
        </div>
    )
}
