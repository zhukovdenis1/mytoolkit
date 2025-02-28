import React, { useContext } from "react";
import { Navigate, useLocation } from "react-router-dom";
import { AuthContext } from "../AuthProvider";
import {Loading} from "@/components/Loading";

export const RequireAuth: React.FC<{ children: React.ReactElement }> = ({ children }) => {
    const auth = useContext(AuthContext);
    const location = useLocation();

    if (auth?.loading) {
        return <Loading />; // Показываем индикатор загрузки
    }

    if (!auth || auth.user === null) {
        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    return children;
};
