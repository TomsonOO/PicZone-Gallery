import React from 'react';
import Slider from 'react-slick';
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const ImageSlider = ({ images }) => {
    const sliderSettings = {
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
    };

    return (
        <div className="max-w-5xl mx-auto">
            <Slider {...sliderSettings}>
                {images.map(image => (
                    <div key={image.id} className="p-4">
                        <img src={image.url} alt={image.filename} className="mx-auto" />
                    </div>
                ))}
            </Slider>
        </div>
    );
};

export default ImageSlider;
