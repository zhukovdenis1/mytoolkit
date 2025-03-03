const apiRoutes: Record<string, [string, string]> = {
    "auth.login": ["auth/login", "post"],
    "auth.refresh": ["auth/refresh", "post"],
    "auth.logout": ["auth/logout", "post"],
    "me": ["me", "get"],
    "users": ["users", "get"],
    "notes.search": ["notes", "get"],
    "notes.dropdown": ["notes/getDropDown", "get"],
    "notes.tree": ["notes/tree", "get"],
    "notes.show": ["notes/:note_id", "get"],
    "notes.add": ['notes', "post"],
    "notes.edit": ['notes/:note_id', "put"],
    "notes.delete": ['notes/:note_id', "delete"],
    "notes.categories": ["notes/categories", "get"],
    "notes.categories.show": ["notes/categories/:category_id", "get"],
    "notes.categories.all": ["notes/categories/all", "get"],
    "notes.categories.tree": ["notes/categories/tree", "get"],
    "notes.categories.edit": ["notes/categories/:category_id", "put"],
    "notes.categories.add": ["notes/categories", "post"],
    "notes.categories.delete": ["notes/categories/:category_id", "delete"],
};

export default apiRoutes;
