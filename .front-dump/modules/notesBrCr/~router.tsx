import { Route, Routes } from "react-router-dom";
import CategoryPage from "./pages/CategoryPage";
import CategoryAddPage from "./pages/CategoryAddPage";
import NoteAddPage from "./pages/NoteAddPage";
import NoteEditPage from "./pages/NoteEditPage";
import CategoryEditPage from "./pages/CategoryEditPage";
import {NotFoundPage} from "@/pages/NotFound.tsx";

const NoteRoutes = () => {
    return (
        <Routes>
            <Route path="/" element={<CategoryPage />} />
            <Route path="/category/:category_id" element={<CategoryPage />} />
            <Route path="/category/add" element={<CategoryAddPage />} />
            <Route path="/category/:category_id/edit" element={<CategoryEditPage />} />
            <Route path="/add" element={<NoteAddPage />} />
            <Route path="/note/:note_id/edit" element={<NoteEditPage />} />
            <Route path="*" element={<NotFoundPage />} />
        </Routes>
    );
};

export default NoteRoutes;
