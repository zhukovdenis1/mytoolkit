import React, {useEffect, useState} from "react";
import {Button, Input, Space, Upload, message} from "ui";
import config from '@/config/config';
import {PaperClipOutlined, UploadOutlined} from "@ui/icons"
import {api} from "api"
import {UploadFile} from "antd";
import {FileRouts} from './types'

export type ImageEditorData = {
    src: string;
    width?: string;
    height?: string;
    storeId?: number;
    origWidth?: number;
    origHeight?: number;
    size?: number;
}

type ImageEditorProps = {
    data: ImageEditorData;
    onChange: (data: ImageEditorData) => void;
    disabled?: boolean;
    mode: string;
    uploadFilesUrl?: string;
    routes: FileRouts;
};

const ImageEditor: React.FC<ImageEditorProps> = ({ data, onChange, disabled, mode, routes }) => {
    disabled;

    const initialFile: UploadFile<any> = {
        uid: '-1', // Уникальный идентификатор
        name: '', // Имя файла
        status: 'done', // Статус файла
        size: 0, // Размер файла
        type: '', // Тип файла
    };

    const [link, setLink] = useState('');
    const [file, setFile] = useState<UploadFile<any>>(initialFile);

    const [image, setImage] = useState(data);

    useEffect(() => {
        if (data != image) {
            onChange(image)
        }
    }, [image]);


    const imgSrc = getImgSrc(image);
    const img = imgSrc
        ? <img alt="" width={image.width ?? ''} height={image.height ?? ''} src={imgSrc} />
        : '';

    const uploadAndUpdateImg = async () => {
        const response = await uploadImg(link, file, routes);
        if (response.success) {
            const data = response.data
            setImage({
                ...image,
                src: data.path,
                origWidth: data.width,
                origHeight: data.height,
                size: data.size,
                storeId: data.store_id,
            })
        } else {
            setFile(initialFile);
            setLink('');
        }
    };

    const formBox = (mode == 'view')
        ? ''
        : <>
            <Space direction="vertical">
                <Space wrap={true}>
                    <Input
                        placeholder="URL"
                        disabled={!!image.storeId}
                        value={image.src}
                        onChange={(e) => {setImage({...image, src: e.target.value})}}
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
                </Space>

                {image.storeId ? '' : (
                    <Space wrap={true}>
                        <Button
                            type="dashed"
                            htmlType="button"
                            icon={<UploadOutlined />}
                            onClick={uploadAndUpdateImg}
                        > Upload</Button>
                        <Input
                            name="link"
                            placeholder="Insert Link"
                            value={link}
                            onChange={(e) => setLink(e.target.value)}
                        />
                        or
                        <Upload
                            name="file"
                            maxCount={1}
                            beforeUpload={() => false}// Отменяем автоматическую загрузку
                            onChange={(info) => {setFile(info.fileList[0])}}
                        >
                            <Button icon={<PaperClipOutlined />}>Choose file</Button>
                        </Upload>
                    </Space>
                )}
            </Space>
          </>

    return (
        <div className="image-editor">
            <div>{img}</div>
            <div>{formBox}</div>
        </div>
    );
}


const uploadImg = async (link: string, file: UploadFile<any>, routes: FileRouts) => {

    if (!link && !file) {
        message.error('Link or file required');
        return;
    }
    const formData = file?.originFileObj ? {file: file.originFileObj} : {};
    //const data = {store_id: 1, note_id: 3, link: file?.originFileObj ? '' : link, type: 'image'}
    const data = {store_id: 1,...routes.save.data, link: file?.originFileObj ? '' : link, type: 'image'}
    //const response = await api.safeRequest("notes.files.add", data, formData);
    const response = await api.safeRequestWithAlert(routes.save.route, data, formData);

    return response.data;
};

const getImgSrc = (image: ImageEditorData): string => {
    var src = '';
    if (image.src) {
        if (image.storeId == 1) {
            src = `${config.baseUrl}/${image.src}`;
        } else {
            src = image.src;
        }
    }

    return src;
}

export default ImageEditor;
