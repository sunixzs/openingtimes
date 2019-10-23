"use strict";
const gulp = require("gulp");
const sass = require("gulp-sass");
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const color = require("gulp-color");

gulp.task("scss", function() {
    let scssPlugins = [autoprefixer()];
    let source = "./styles.scss";
    let target = "./";

    console.log(color("scss -> css: ", "BLUE") + color(source, "CYAN"));
    console.log(color("         to: ", "BLUE") + color(target, "CYAN"));

    return (
        gulp
            .src(source)
            .pipe(
                sass({
                    indentType: "space",
                    indentWidth: 4,
                    sourceMap: false,
                    outputStyle: "expanded"
                }).on("error", sass.logError)
            )
            .pipe(postcss(scssPlugins))
            .pipe(gulp.dest(target))
    );
});
