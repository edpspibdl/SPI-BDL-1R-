from flask import Flask
from routes.lpp_vs_plano import lpp_bp
from routes.margin_minus import marmin_bp # 🔹 import blueprint baru

app = Flask(__name__)

# Register blueprints
app.register_blueprint(lpp_bp)
app.register_blueprint(marmin_bp)  # 🔹 register blueprint baru

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
