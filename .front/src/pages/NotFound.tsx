import { Link } from 'react-router-dom'
import {route} from "api";
const NotFoundPage = () => {
    return (
        <div>
            This page doesn't exist. Go <Link to={route('user')}>home</Link>
        </div>
    )
}

export {NotFoundPage};
