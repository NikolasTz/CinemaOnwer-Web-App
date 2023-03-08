const mongoose = require('mongoose');
const Schema = mongoose.Schema;

// Create movies Schema and model
const Movies_Schema = new Schema({

    TITLE:{
        type: String,
        required : true
    },
    STARTDATE:{
        type: Date,
        required : true
    },
    ENDDATE:{
        type: Date,
        required : true
    },
    CINEMANAME:{
        type: String,
        required : true
    },
    CATEGORY:{
        type: String,
        required : true
    },
    CINEMAOWNERID:{
        type: String,
        required : true
    }
});

const Movies = mongoose.model('movies', Movies_Schema);

// Export model Movies
module.exports = Movies;
