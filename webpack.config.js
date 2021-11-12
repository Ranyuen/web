"use static";

module.exports = {
  entry: {
    "article_editor.min.js": "src/javascripts/article_editor.js",
    "changeTab.min.js": "src/javascripts/changeTab.js",
    "colorbox.min.js": "src/javascripts/colorbox.js",
    "layout.min": "src/javascripts/layout.js",
    "orchidExam.js": "src/javascripts/orchidExam.js",
    "photoGallery.min": "src/javascripts/photoGallery.js",
    "photo_editor.min.js": "src/javascripts/photo_editor.js",
  },
  output: {},
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [],
      },
      {
        test: /\.less$/,
        use: [],
      },
    ],
  },
  target: ["web"],
};
