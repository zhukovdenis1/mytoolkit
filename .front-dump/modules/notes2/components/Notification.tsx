import React from "react";

const Notification: React.FC<{ message: string | null }> = ({ message }) => {
    if (!message) return null;

    return <div className="notification">{message}</div>;
};

export default Notification;
