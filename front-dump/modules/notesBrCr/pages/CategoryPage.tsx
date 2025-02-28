import React, {FormEvent, useContext} from "react";
import {Link, useParams} from "react-router-dom";
import CategoryList from "../components/CategoryList";
import NoteList from "../components/NoteList";
import SearchForm from "../components/SearchForm";
import styles from "../Notes.module.css";
import {NotesContext} from "../NotesProvider";
import {NotesRoute} from "@/modules/notes/routes.ts";
import {Loading} from "@/components/Loading.tsx";

const CategoryPage = () => {
    const notesContext = useContext(NotesContext);


    const { category_id } = useParams();

    const categoryId = category_id ? parseInt(category_id) : null;

    const handleClick = (e: FormEvent) => {
        e.preventDefault();
        if (notesContext) {
            //notesContext.func();
            console.log(notesContext?.categories);
        }

    };

    return (
        <div className={styles.module}>
            <SearchForm/>
            {notesContext?.flag}
            {notesContext?.categories ? (
                <ul className="">
                    {notesContext.categories.map((category) => (
                        <li key={category.id}>
                            <span><Link to={`/notes/category/${category.id}/`}>{category.name}</Link></span>
                        </li>
                    ))}
                </ul>
            ) : (<Loading />)}


            <button onClick={handleClick}>button</button>
            <CategoryList parentId={categoryId}/>
            <NoteList categoryId={categoryId}/>
        </div>
    );
};

export default CategoryPage;
