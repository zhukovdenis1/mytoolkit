import React, {useEffect, useState} from "react";
import {Input, Space } from "ui";
import {EditorConfig} from './types'

export type ProductEditorData = {
    id?: number;
    id_ae?: string;
    title?: string;
    description?: string;
    props?: string;
    cons?: string;
    img?: string;
}

type ProductEditorProps = {
    data: ProductEditorData;
    onChange: (data: ProductEditorData) => void;
    disabled?: boolean;
    mode: string;
    config: EditorConfig
};


const ProductEditor: React.FC<ProductEditorProps> = ({ data, onChange, disabled, mode, config }) => {
    disabled;
    config;

    const [product, setProduct] = useState(data);

    useEffect(() => {
        if (data != product) {
            onChange(product)
        }
        // if (authContext?.user?.id === 1001) {
        //     setstorageId(2);
        // }

    }, [product]);


    const formBox = (mode == 'view')
        ? ''
        : <>
        <Space direction="vertical" style={{width: '100%'}}>
            <Space wrap={true}>
                <Input
                    placeholder="Id"
                    value={product.id}
                    onChange={(e) => {
                        setProduct({...product, id: Number(e.target.value)})
                    }}
                />
                <Input
                    placeholder="Id ae"
                    value={product.id_ae}
                    onChange={(e) => {
                        setProduct({...product, id_ae: e.target.value})
                    }}
                />
                <Input
                    placeholder="Img"
                    value={product.img}
                    onChange={(e) => {
                        setProduct({...product, img: e.target.value})
                    }}
                />
            </Space>
            <div>
                <Input
                    placeholder="Title"
                    value={product.title}
                    onChange={(e) => {
                        setProduct({...product, title: e.target.value})
                    }}
                />
            </div>
            <div>
                <Input.TextArea
                    placeholder="Description"
                    value={product.description}
                    onChange={(e) => {
                        setProduct({...product, description: e.target.value})
                    }}
                />
            </div>
            <div style={{display: 'flex', width: '100%', gap: '8px'}}>
                <Input.TextArea
                    placeholder="Props"
                    value={product.props}
                    onChange={(e) => {
                        setProduct({...product, props: e.target.value})
                    }}
                />
                <Input.TextArea
                    placeholder="Cons"
                    value={product.cons}
                    onChange={(e) => {
                        setProduct({...product, cons: e.target.value})
                    }}
                />
            </div>
        </Space>
</>

    return (
        <div className="product-editor">
            <div>{formBox}</div>
        </div>
    );
}


export default ProductEditor;
