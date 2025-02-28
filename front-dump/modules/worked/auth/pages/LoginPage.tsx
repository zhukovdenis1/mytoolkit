import React, { useContext, useState, FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { AuthContext } from "../AuthProvider.tsx";

export const LoginPage: React.FC = () => {
    const authContext = useContext(AuthContext);
    const navigate = useNavigate();
    const [email, setEmail] = useState<string>("test@example.com");
    const [password, setPassword] = useState<string>("password");

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        if (authContext) {
            await authContext.signin({ email, password }, () => navigate("/"));
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <h1>Login</h1>
            <input
                type="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
            />
            <input
                type="password"
                placeholder="Password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
            />
            <button type="submit">Login</button>
        </form>
    );
};
