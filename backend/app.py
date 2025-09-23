from flask import Flask
from routes.lpp_vs_plano import lpp_bp

app = Flask(__name__)

# Register blueprint
app.register_blueprint(lpp_bp)

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)
