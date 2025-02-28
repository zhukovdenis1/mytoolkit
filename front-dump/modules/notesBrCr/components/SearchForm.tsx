import React, { useState } from "react";
import { useNavigate } from "react-router-dom";

const SearchForm = () => {
    const [search, setSearch] = useState("");
    const [where, setWhere] = useState("notes");
    const navigate = useNavigate();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        let query = `?search=${search}`;
        if (where !== "notes") query += `&parent_id=null`; // Для категорий
        navigate(`/notes${query}`);
    };

    const handleReset = (e: React.FormEvent) => {
        e.preventDefault();
        setSearch("");
        navigate(`/notes`);
    };

    return (
        <form className="searchForm" onSubmit={handleSubmit}>
            <input
                type="text"
                name="search"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Search"
            />
            <select
                name="where"
                value={where}
                onChange={(e) => setWhere(e.target.value)}
            >
                <option value="notes">Notes</option>
                <option value="categories">Categories</option>
            </select>
            <button type="submit">Search</button>
            <button type="button" onClick={handleReset}>
                Reset
            </button>
        </form>
    );
};

export default SearchForm;
