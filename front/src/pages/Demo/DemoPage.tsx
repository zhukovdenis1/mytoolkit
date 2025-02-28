import { Link} from "react-router-dom";
import {route} from 'api'

const DemoPage = () => {
    return (
        <div>
            <p>
                <Link to={route('demo.tree')}>Tree Categories</Link>
                &nbsp;-&nbsp;
                Древовидная структура с поиском. База для индексной страницы категорий notes
            </p>

            <p>
                <Link to={route('demo.note_category_list')}>DemoNoteCategoryListPage</Link>
                &nbsp;-&nbsp;
                Древовидная структура без ANT
            </p>
        </div>
    )
}

export {DemoPage}
