// front/src/utils/convertRoutes.ts
import { RouteObject } from 'react-router-dom';
import React from 'react';

const convertRouteToObject = (route: React.ReactElement): RouteObject => {
    const { path, element, children } = route.props;

    return {
        path,
        element,
        children: children ? React.Children.toArray(children).map(convertRouteToObject) : [],
    };
};

export const convertRoutesToObject = (routes: React.ReactElement[]): RouteObject[] => {
    return routes.map(convertRouteToObject);
};
