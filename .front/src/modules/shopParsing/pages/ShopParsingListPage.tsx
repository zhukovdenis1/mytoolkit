import React from "react";
import { ButtonLink } from "ui";
import { route } from "api";


export const ShopParsingListPage: React.FC = () => {
    return (
        <>
            <div className="button-box">
                <ButtonLink type="primary2" to={route('shop.parsing.add')}>Add</ButtonLink>
            </div>
        </>
    );
};

