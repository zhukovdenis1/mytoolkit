import React, {useEffect, useState} from "react";
import {Input, Space} from "ui";


export type VideoEditorData = {
    src: string;
    width?: string;
    height?: string;
};

type VideoEditorProps = {
    data: VideoEditorData;
    onChange: (value: VideoEditorData) => void;
    disabled?: boolean;
    mode: string
};

const VideoEditor: React.FC<VideoEditorProps> = ({ data, onChange, disabled, mode }) => {
    disabled;
    // let videoJson: VideoEditorData = {src: ''}
    // try {
    //     videoJson = JSON.parse(value)
    // } catch (e) {}

    const [video, setVideo] = useState(data);

    useEffect(() => {
        if (data != video) {
            onChange(video)
        }
    }, [video]);


    const frame = video?.src
        ? <iframe width={video.width ?? '560'} height={video.height ?? '315'} src={video.src} title="YouTube video player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerPolicy="strict-origin-when-cross-origin" allowFullScreen></iframe>
        : '';

    const formBox = (mode == 'view')
        ? ''
        : <>
            <Space>
                <Input
                    placeholder="URL"
                    value={video?.src}
                    onChange={(e) => {setVideo({...video, src: convertYouTubeLink(e.target.value)})}}
                />
                <Input
                    placeholder="width"
                    value={video?.width}
                    onChange={(e) => {setVideo({...video, width: e.target.value})}}
                />
                <Input
                    placeholder="height"
                    value={video?.height}
                    onChange={(e) => {setVideo({...video, height: e.target.value})}}
                />
            </Space>
          </>

    return (
        <div className="video-editor">
            <div>{frame}</div>
            <div>{formBox}</div>
        </div>
    );
}

export default VideoEditor;

const convertYouTubeLink = function(src: string) {
    const pattern = /^https:\/\/www\.youtube\.com\/watch\?v=([\w-]+)/;
    const match = src.match(pattern);

    if (match) {
        const videoId = match[1];
        return `https://www.youtube.com/embed/${videoId}`;
    }

    return src; // если ссылка не подходит, возвращаем как есть
}
