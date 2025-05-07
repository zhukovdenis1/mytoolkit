type RouteData = {
    route: string,
    data?: Record<string, any>
}

export type FileRouts = {
    save: RouteData,
    delete: RouteData
}

type ImageEditorConfig = {
    storageId?: number,
}

export type EditorConfig = {
    image?: ImageEditorConfig,
    fileRoutes: FileRouts
}
