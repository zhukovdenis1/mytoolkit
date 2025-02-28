import React from "react";
//import { useState } from "react";
//import {api} from "../services/api";

// interface User {
//     id: number;
//     name: string;
//     email: string;
// }

const HomePage: React.FC = () => {
    //const [users, setUsers] = useState<User[]>([]);
    //alert(config.apiUrl)
    const fetchUsers = async () => {
        // try {
        //     const { data } = await api.get<{ users: User[] }>("/users");
        //     setUsers(data.users);
        // } catch (error) {
        //     console.error("Failed to fetch users:", error);
        // }
    };

    return (
        <div>
            <h1>Home</h1>
            <button onClick={fetchUsers}>Get Users</button>
            <ul>
                {/*{users.map((user) => (*/}
                {/*    <li key={user.id}>*/}
                {/*        {user.name} - {user.email}*/}
                {/*    </li>*/}
                {/*))}*/}
            </ul>
        </div>
    );
};

export {HomePage}
