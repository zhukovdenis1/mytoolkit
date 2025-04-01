type RouteData = {
    route: string,
    data?: Record<string, any>
}

export type FileRouts = {
    save: RouteData,
    delete: RouteData
}
