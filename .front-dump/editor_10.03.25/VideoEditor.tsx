import React, {useEffect, useState} from "react";
import {Input, Space} from "ui";

type VideoEditorProps = {
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
    mode: string
};

type VideoProps = {
    url: string;
    width?: string;
    height?: string;
};

const VideoEditor: React.FC<VideoEditorProps> = ({ value, onChange, disabled, mode }) => {
    disabled;
    let videoJson: VideoProps = {url: ''}
    try {
        videoJson = JSON.parse(value)
    } catch (e) {}

    const [video, setVideo] = useState(videoJson);

    useEffect(() => {
        //console.log(JSON.stringify(video))
        onChange(JSON.stringify(video))
    }, [video]);


    const frame = video.url
        ? <iframe width={video.width ?? '560'} height={video.height ?? '315'} src={video.url} title="YouTube video player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerPolicy="strict-origin-when-cross-origin" allowFullScreen></iframe>
        : '';

    const formBox = (mode == 'view')
        ? ''
        : <>
            <Space>
                <Input
                    placeholder="URL"
                    value={video.url}
                    onChange={(e) => {setVideo({...video, url: convertYouTubeLink(e.target.value)})}}
                />
                <Input
                    placeholder="width"
                    value={video.width}
                    onChange={(e) => {setVideo({...video, width: e.target.value})}}
                />
                <Input
                    placeholder="height"
                    value={video.height}
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

const convertYouTubeLink = function(url: string) {
    const pattern = /^https:\/\/www\.youtube\.com\/watch\?v=([\w-]+)/;
    const match = url.match(pattern);

    if (match) {
        const videoId = match[1];
        return `https://www.youtube.com/embed/${videoId}`;
    }

    return url; // если ссылка не подходит, возвращаем как есть
}
