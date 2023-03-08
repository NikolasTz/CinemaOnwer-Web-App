const express = require ('express');
const router = express.Router();
const Movies = require('../models/movies');
const Favorites = require('../models/favorites');
const Subscription = require('../models/subscriptions');

// Routes for movies

// Get a list of movies from the db
router.get('/movies', function(req, res, next){

    // Find all movies by title
    if( typeof req.query.title !== 'undefined' ){     
        Movies.find({TITLE: req.query.title}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find all movies by startdate
    else if( typeof req.query.startdate !== 'undefined' ){        
        Movies.find({STARTDATE: req.query.startdate}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find all movies by enddate
    else if( typeof req.query.enddate !== 'undefined' ){    
        Movies.find({ENDDATE: req.query.enddate}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find all movies by cinemaname
    else if( typeof req.query.cinemaname !== 'undefined' ){
        Movies.find({CINEMANAME: req.query.cinemaname}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find all movies by category
    else if( typeof req.query.category !== 'undefined' ){      
        Movies.find({CATEGORY: req.query.category}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find movie by id
    else if( typeof req.query.id !== 'undefined' ){      
        Movies.findById({_id: req.query.id}).then(function(movie){
            res.send(movie);
        }).catch(next);
    }
    // Find all movies by cinemaonwerid
    else if( typeof req.query.cinemaownerid !== 'undefined' ){      
        Movies.find({CINEMAOWNERID: req.query.cinemaownerid}).then(function(movies){
            res.send(movies);
        }).catch(next);
    }
    // Find all available movies
    else{    
        Movies.find({}).then(function(movies){
            res.send(movies);
        }).catch(next)
    }
});

// Add a new movie to the db
router.post('/movies', function(req, res, next){
    Movies.create(req.body).then(function(movie){
        res.send(movie);
    }).catch(next);
});

// Update a movie in the db
router.put('/movies/:id', function(req, res, next){
    Movies.findByIdAndUpdate({_id: req.params.id}, req.body).then(function(){
        // Return the updated movie
        Movies.findOne({_id: req.params.id}).then(function(movie){
            res.send(movie);
        });
    }).catch(next);
});

// Delete a movie from the db
router.delete('/movies/:id', function(req, res, next){
    Movies.findByIdAndRemove({_id: req.params.id}).then(function(movie){
        // Return the deleted movie
        res.send(movie);
    }).catch(next);
});

// Routes for favorites

// Get a list of favorite from the db
router.get('/favorites', function(req, res, next){

    // Get the favorite movie by userid and movieid
    if( (typeof req.query.movieid !== 'undefined') && typeof req.query.userid !== 'undefined' ){
        Favorites.find({$and:[{MOVEID: req.query.movieid},{USERID: req.query.userid}]}).then(function(favorite){
            res.send(favorite);
        }).catch(next);
    }
    // Get the favorites movies by userid
    else{
        Favorites.aggregate([{
                                $match : { USERID: req.query.userid } },
                                { $addFields: { MOVEID : { $toObjectId: "$MOVEID" }}},
                                { $lookup:{
                                    from: "movies",
                                    localField: "MOVEID",
                                    foreignField: "_id",
                                    as: "join_res"
                                }},
                                {
                                  $replaceRoot: { newRoot: { $mergeObjects: [ { $arrayElemAt: [ "$join_res", 0 ] }, "$$ROOT" ] } }
                                },
                                { $project: { join_res: 0 } 
                            } 
                            ])
                            .then(function(fav_movies){
                                res.send(fav_movies);
                            }).catch(next);
    } 
});

// Add a new favorite to the db
router.post('/favorites', function(req, res, next){
    Favorites.create(req.body).then(function(favorite){
        // Return the new favorite movie
        res.send(favorite);
    }).catch(next);
});

// Update a favorite in the db
router.put('/favorites/:id', function(req, res, next){
    Favorites.findByIdAndUpdate({_id: req.params.id}, req.body).then(function(){
        // Return the updated favorite movie
        Favorites.findOne({_id: req.params.id}).then(function(favorite){
            res.send(favorite);
        });
    }).catch(next);
});

// Delete a favorite from the db
router.delete('/favorites/:id', function(req, res, next){
    // Delete many favorites using movieid
    if( (typeof req.query.movieid !== 'undefined')){
        Favorites.deleteMany({ MOVEID: { $eq: req.query.movieid} }).then(function(favorites){
            // Return the deleted favorite
            res.send(favorites);
        }).catch(next);
    }
    // Delete a favorite movie by id
    else{
        Favorites.findByIdAndRemove({_id: req.params.id}).then(function(favorite){
            // Return the deleted favorite
            res.send(favorite);
        }).catch(next);
    }
});


// Routes for subscriptions

// Get all subscriptions
router.get('/subscriptions', function(req, res, next){

    // Find all subscriptions by movieid and sort by ENDDATE
    if( typeof req.query.movieid !== 'undefined' ){     
        Subscription.find({ 'data.id': req.query.movieid}).sort({"data.ENDDATE":-1}).then(function(subscriptions){
            res.send(subscriptions);
        }).catch(next);
    }
    // Find all subscriptions
    else{
        Subscription.find({}).then(function(subscriptions){
            res.send(subscriptions);
        }).catch(next);
    }
});

// Add a new subscription to the db
router.post('/subscriptions', function(req, res, next){
    Subscription.create(req.body).then(function(subscription){
        // Return the new subscription
        res.send(subscription);
    }).catch(next);
});

// Update a subscription in the db
router.put('/subscriptions/:id', function(req, res, next){
    Subscription.findByIdAndUpdate({_id: req.params.id}, req.body).then(function(){
        // Return the updated subscription
        Subscription.findOne({_id: req.params.id}).then(function(subscription){
            res.send(subscription);
        });
    }).catch(next);
});

// Delete a subscription from the db
router.delete('/subscriptions/:id', function(req, res, next){
    Subscription.findByIdAndRemove({_id: req.params.id}).then(function(subscription){
        // Return the deleted subscription
        res.send(subscription);
    }).catch(next);
});

// Export the routes
module.exports = router;
