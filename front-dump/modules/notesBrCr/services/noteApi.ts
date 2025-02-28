import api from "../../../services/api";

api.fetchCategories = () => api.get("/note/categories/all");
api.fetchNotes = (categoryId: number) => api.get(`/notes?category=${categoryId}`);
api.deleteCategory = (categoryId: number) => api.delete(`/note/categories/${categoryId}`);
api.deleteNote = (noteId: number) => api.delete(`/notes/${noteId}`);

export default api;
