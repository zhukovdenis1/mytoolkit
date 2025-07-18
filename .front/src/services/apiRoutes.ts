const apiRoutes: Record<string, [string, string]> = {
    "auth.login": ["auth/login", "post"],
    "auth.refresh": ["auth/refresh", "post"],
    "auth.logout": ["auth/logout", "post"],
    "me": ["me", "get"],
    "users": ["users", "get"],
    "notes.search": ["notes", "get"],
    "notes.dropdown": ["notes/get-dropdown", "get"],
    "notes.tree": ["notes/tree", "get"],
    "notes.parents": ["notes/parents", "get"],
    "notes.show": ["notes/:note_id", "get"],
    "notes.add": ['notes', "post"],
    "notes.edit": ['notes/:note_id', "put"],
    "notes.editContent": ['notes/:note_id/edit-content', "put"],
    "notes.delete": ['notes/:note_id', "delete"],
    "notes.categories": ["notes/categories", "get"],
    "notes.categories.show": ["notes/categories/:category_id", "get"],
    "notes.categories.all": ["notes/categories/all", "get"],
    "notes.categories.tree": ["notes/categories/tree", "get"],
    "notes.categories.edit": ["notes/categories/:category_id", "put"],
    "notes.categories.add": ["notes/categories", "post"],
    "notes.categories.delete": ["notes/categories/:category_id", "delete"],
    "notes.files.add": ["notes/:note_id/files", "post"],

    "files.delete": ["files/:file_id", "delete"],

    "main.links": ["main/links", "get"],

    "admin.shop.siteList": ["admin/shop/site-list", "get"],
    "admin.shop.articles.list": ["admin/shop/articles", "get"],
    "admin.shop.articles.show": ["admin/shop/articles/:article_id", "get"],
    "admin.shop.articles.pubInfo": ["admin/shop/articles/:article_id/pub-info", "get"],
    "admin.shop.articles.add": ["admin/shop/articles", "post"],
    "admin.shop.articles.edit": ["admin/shop/articles/:article_id", "put"],
    "admin.shop.articles.editContent": ["admin/shop/articles/:article_id/edit-content", "put"],
    "admin.shop.articles.delete": ["admin/shop/articles/:article_id", "delete"],
    "admin.articles.files.add": ["admin/shop/articles/:article_id/files", "post"],

    "admin.shop.parsing.add": ["admin/shop/parsing", "post"],
    "admin.shop.parsing.getEpnCategories": ["admin/shop/parsing/epn-categories", "get"],
};

export default apiRoutes;
