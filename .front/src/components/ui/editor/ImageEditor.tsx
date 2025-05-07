import React, {useContext, useEffect, useState} from "react";
import {Button, Input, Select, Space, Upload, message} from "ui";
import config from '@/config/config';
import {PaperClipOutlined, UploadOutlined} from "@ui/icons"
import { FolderOutlined, SendOutlined, LockOutlined, UnlockOutlined } from '@ant-design/icons';
import {api} from "api"
import {UploadFile} from "antd";
import {EditorConfig} from './types'
import {AuthContext} from "@/modules/auth/AuthProvider.tsx";//времменное решение

export type ImageEditorData = {
    src: string;
    width?: string;
    height?: string;
    fileId?: number;
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
    //routes: FileRouts;
    config: EditorConfig
};


const ImageEditor: React.FC<ImageEditorProps> = ({ data, onChange, disabled, mode, config }) => {
    disabled;
    const authContext = useContext(AuthContext);

    const initialFile: UploadFile<any> = {
        uid: '-1', // Уникальный идентификатор
        name: '', // Имя файла
        status: 'done', // Статус файла
        size: 0, // Размер файла
        type: '', // Тип файла
    };

    const [link, setLink] = useState('');
    const [isPrivate, setIsPrivate] = useState(0);
    const [storageId, setstorageId] = useState(3);
    const [file, setFile] = useState<UploadFile<any>>(initialFile);
    const [image, setImage] = useState(data);

    useEffect(() => {
        if (data != image) {
            onChange(image)
        }
        // if (authContext?.user?.id === 1001) {
        //     setstorageId(2);
        // }

        if (config.image?.storageId) {
            setstorageId(config.image?.storageId ?? 3);
        }

    }, [image]);


    //const imgSrc = getImgSrc(image);
    const imgSrc = getImgSrc(image);
    const img = imgSrc
        ? <img alt="" width={image.width ?? ''} height={image.height ?? ''} src={imgSrc} />
        : '';

    const uploadAndUpdateImg = async () => {
        const response = await uploadImg(storageId, isPrivate, link, file, config);

        if (response.success) {
            const data = response.data?.data;
            //console.log(data)
            setImage({
                ...image,
                src: data?.url_inline,
                origWidth: data?.extra.width,
                origHeight: data?.extra.height,
                size: data?.size,
                fileId: data?.id,
            })
        } else {
            // setFile(initialFile);
            // setLink('');
        }
    };

    const formBox = (mode == 'view')
        ? ''
        : <>
            <Space direction="vertical">
                <Space wrap={true}>
                    <Input
                        placeholder="URL"
                        disabled={!!image.src}
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

                {image.src ? '' : (
                    <Space wrap={true}>
                        {authContext?.user?.id !== 1001 ? (<Input type="hidden" value="3" />) : (
                            <Select
                                title="Store type"
                                value={storageId.toString()}
                                onChange={(value) => setstorageId(parseInt(value))}
                            >
                                <Select.Option value="1" title="hosting">
                                    <FolderOutlined />
                                </Select.Option>
                                <Select.Option value="2" title="telegram">
                                    <SendOutlined />
                                </Select.Option>
                            </Select>
                        )}
                        <Select
                            title="Publicity"
                            value={isPrivate ? "1" : "0"}
                            onChange={(value) => setIsPrivate(value == "1" ? 1 : 0)}
                        >
                            <Select.Option value="1" title="private">
                                <LockOutlined />
                            </Select.Option>
                            <Select.Option value="0" title="public">
                                <UnlockOutlined />
                            </Select.Option>
                        </Select>
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


const uploadImg = async (storageId: number, isPrivate: number, link: string, file: UploadFile<any>, config: EditorConfig) => {

    if (!link && !file) {
        message.error('Link or file required');
        return {
            success: false,
            data: null
        };
    }
    const formData = file?.originFileObj ? {file: file.originFileObj} : {};
    //const data = {storage_id: 1, note_id: 3, link: file?.originFileObj ? '' : link, type: 'image'}
    const data = {
        storage_id: storageId,
        ...config.fileRoutes.save.data,
        link: file?.originFileObj ? '' : link,
        private: isPrivate,
        type: 'image'
    }
    //const response = await api.safeRequest("notes.files.add", data, formData);
    return await api.safeRequestWithAlert(config.fileRoutes.save.route, data, formData);
};

const getImgSrc = (image: ImageEditorData): string => {
    var src = '';
    if (image.src) {
        if (image.src[0] == '/') {
            src = `${config.baseUrl}${image.src}`;
        } else {
            src = image.src;
        }
    }

    return src;
}

export default ImageEditor;
