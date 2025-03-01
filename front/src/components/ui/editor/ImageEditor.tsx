import React, {useEffect, useState} from "react";
import {Button, Input, Space} from "ui";

type ImageEditorProps = {
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
    mode: string
};

type ImageProps = {
    url: string;
    width?: string;
    height?: string;
};

const ImageEditor: React.FC<ImageEditorProps> = ({ value, onChange, disabled, mode }) => {
    disabled;
    let imageJson: ImageProps = {url: ''}
    try {
        imageJson = JSON.parse(value)
    } catch (e) {}

    const [image, setImage] = useState(imageJson);

    useEffect(() => {
        //console.log(JSON.stringify(image))
        onChange(JSON.stringify(image))
    }, [image]);


    const img = image.url
        ? <img alt="" width={image.width ?? '560'} height={image.height ?? '315'} src={image.url} />
        : '';

    const formBox = (mode == 'view')
        ? ''
        : <>
            <Space>
                <Input
                    placeholder="URL"
                    value={image.url}
                    onChange={(e) => {setImage({...image, url: e.target.value})}}
                />
                <Input
                    placeholder="width"
                    value={image.width}
                    onChange={(e) => {setImage({...image, width: e.target.value})}}
                />
                <Input
                    placeholder="height"
                    value={image.height}
                    onChange={(e) => {setImage({...image, height: e.target.value})}}
                />
                <Button
                    type="default"
                    htmlType="button"
                    onClick={() => {alert('save')}}
                >
                    Save
                </Button>
            </Space>
          </>

    return (
        <div className="image-editor">
            <div>{img}</div>
            <div>{formBox}</div>
        </div>
    );
}

export default ImageEditor;


