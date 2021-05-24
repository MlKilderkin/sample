import React, {useState} from "react";
import {Button, Form, Row, Col} from "react-bootstrap";

const settings = [
    {
        id: 0,
        property: 'width',
        default: 1200,
        min: 0,
        max: 1600,
        step: 10,
    }, {
        id: 1,
        property: 'brightness',
        default: 1,
        min: 0,
        max: 3,
        step: 0.01
    }, {
        id: 2,
        property: 'saturate',
        default: 100,
        min: 0,
        max: 200,
        step: 10,
    }, {
        id: 3,
        property: 'contrast',
        default: 100,
        min: 50,
        max: 150,
        step: 10,
    }, {
        id: 4,
        property: 'sepia',
        default: 0,
        min: 0,
        max: 100,
        step: 10,
    }
];
const filters = [
    {
        id: 0,
        name: "Original",
        settings: {
            brightness: 1,
            saturate: 100,
            contrast: 100,
            sepia: 0
        }
    },
    {
        id: 1,
        name: 'B&W aka "Rhea"',
        settings: {
            brightness: 1,
            saturate: 0,
            contrast: 100,
            sepia: 0,
        }
    },
    {
        id: 2,
        name: 'Deep Fried Ice Cream',
        settings: {
            brightness: 1,
            saturate: 200,
            contrast: 150,
            sepia: 0
        }
    },
    {
        id: 3,
        name: 'Old Timey',
        settings: {
            brightness: 1,
            saturate: 100,
            contrast: 100,
            sepia: 100
        }
    }
];

const ImageEditor = ({image, onSave}) => {
    const [width, setWidth] = useState(image.width || 400);
    const [brightness, setBrightness] = useState(image.brightness || 1);
    const [saturate, setSaturate] = useState(image.saturate || 100);
    const [contrast, setContrast] = useState(image.contrast || 100);
    const [sepia, setSepia] = useState(image.sepia || 0);

    const resetToDefault = () => {
        setWidth(400);
        setBrightness(1);
        setSaturate(100);
        setContrast(100);
        setSepia(0);
    }

    const imgStyle = {
        width: `${width}px`,
        filter: `brightness(${brightness}) saturate(${saturate}%) contrast(${contrast}%) sepia(${sepia})`
    };

    const handleChange = (e, prop) => {
        if (prop === 'filter') {
            const filter = filters.filter(item => item.id === e);;
            setBrightness(filter[0].settings.brightness);
            setSaturate(filter[0].settings.saturate);
            setContrast(filter[0].settings.contrast);
            setSepia(filter[0].settings.sepia);
            return;
        }

        switch (prop) {
            case 'width':
                setWidth(e.target.value);
                return;
            case 'brightness':
                setBrightness(e.target.value);
                return;
            case 'saturate':
                setSaturate(e.target.value);
                return;
            case 'sepia':
                setSepia(e.target.value);
                return;
            case 'contrast':
                setContrast(e.target.value);
                return;
        }
    };

    const handleSettingValue = prop => {
        switch (prop) {
            case 'width':
                return width;
            case 'brightness':
                return brightness;
            case 'saturate':
                return saturate;
            case 'sepia':
                return sepia;
            case 'contrast':
                return contrast;
        }
    };

    return (
        <div className="image-editor">
            <Row>
                <Col xs={6} sm={6} md={3}>
                    <Button onClick={resetToDefault}>Reset to default</Button></Col>
                <Col xs={6} sm={6} md={{span: 3, offset:6}}>
                    <Button variant="success" onClick={e => onSave(width, brightness, saturate, contrast, sepia)}>
                        Save
                    </Button>
                </Col>
            </Row>


            <Row className="image-editor__container">
                <Col xs={12} sm={12} md={3} lg={2}>
                {settings.map(setting => (
                    <div className="image-editor__setting" key={setting.id}>
                        <Form>
                            <Form.Group controlId={setting.property}>
                                <Form.Label>{setting.property}</Form.Label>
                                <Form.Control
                                    type="range"
                                    value={handleSettingValue(setting.property)}
                                    onChange={e => handleChange(e, setting.property)}
                                    min={setting.min}
                                    max={setting.max}
                                    step={setting.step}
                                />
                            </Form.Group>
                        </Form>
                    </div>
                ))}
                </Col>
                <Col xs={12} sm={12} md={6} lg={8}>
                    <div className="image-editor__preview">
                        <img src={image.blob || image.img_original} style={imgStyle} />
                    </div>
                </Col>
                <Col xs={12} sm={12} md={3} lg={2}>
                    <div className="image-editor__filters">
                        {filters.map((filter, index) => (
                            <div key={index}>
                                <p>{filter.name}</p>
                                <div className="image-editor__filter" key={index} onClick={e => handleChange(filter.id, 'filter')}>
                                    <img
                                        className="image-editor__filter-image"
                                        style={{
                                            filter: `brightness(${filter.settings.brightness || 1})  saturate(${filter.settings.saturate || 100}%) contrast(${filter.settings.contrast || 100}%) sepia(${filter.settings.sepia || 0})`
                                        }}
                                        src={image.blob || image.img_original} />

                                </div>
                            </div>
                        ))}
                    </div>
                </Col>

            </Row>

        </div>
    );
};

export default ImageEditor;
