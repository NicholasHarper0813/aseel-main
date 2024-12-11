import React from 'react';
import ReactDOM from 'react-dom';
var LoadingIcon = require('./Loading_icon.gif');

// Load the CSS
require('./style.scss');

class Posts extends React.Component {
    constructor(props) {
        super(props);
        this.getMorePosts = this.getMorePosts.bind(this);
        this.state = {
            posts: {},
            page: 0,
            getPosts: true
        }
    }

    componentDidMount() {
        var that = this;
        window.onbeforeunload = function() {window.scrollTo(0,0);}

        // init controller
        var controller = new ScrollMagic.Controller();

        // build scene
        var scene = new ScrollMagic.Scene({ triggerElement: "#posts-here", triggerHook: "onEnter" })
            .addTo(controller)
            .on("enter", function (e) {
                if (that.state.getPosts) {
                    that.getMorePosts();
                }
            });
    }

    getMorePosts() {
        var that = this;
        var totalPages;
        // adding a loader
        jQuery("#loader").addClass("active");
        this.setState({ page: this.state.page + 1 });

        fetch("http://localhost/aseel/wp-json/wp/v2/posts/?page=" + this.state.page)
            .then(function (response) {
                for (var pair of response.headers.entries()) {
                    if (pair[0] == 'x-wp-totalpages') {
                        totalPages = pair[1];
                    }
                    if (that.state.page == totalPages) {
                        that.state.getPosts = false;
                    }
                }
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            })
            .then(function (results) {
                jQuery.each(results, function (i, field) {
                    jQuery(".posts-container").
                        append("<article class='post-excerpt'><a href='" + field.link + "'><h2>" + field.title.rendered + "</h2></a><p>" + field.excerpt.rendered + "</p></article>");
                    if (results[0] == null) {
                        jQuery("#loader").remove();
                    }
                    // removing loader
                    jQuery("#loader").removeClass("active");
                    var controller2 = new ScrollMagic.Controller();
                    // loop through each .posts-container .post-excerpt element
                    jQuery('.posts-container .post-excerpt').each(function () {

                        // build a scene
                        var ourScene2 = new ScrollMagic.Scene({
                            triggerElement: this.children[0],
                            reverse: false,
                            triggerHook: 1
                        })
                            .setClassToggle(this, 'fade-in') // add class to project01
                            .addTo(controller2);
                    });
                });
            }).catch(function (error) {
                console.log('There has been a problem with your fetch operation: ' + error.message);
                jQuery("#loader").remove();

            });
    }

    render() {
        return (
            <div>
                <div className="posts-container"></div>
                <div id="posts-here"></div>
                <div id="loader">
                    <img src={LoadingIcon} />
                </div>
            </div>
        )
    }
}

ReactDOM.render(<Posts />, document.getElementById('app-container'));