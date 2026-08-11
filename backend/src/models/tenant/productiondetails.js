const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('productiondetails', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    production_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    materialid: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    material_desginqty: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    material_issuedqty: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    material_consumedqty: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    balance: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    }
  }, {
    sequelize,
    tableName: 'productiondetails',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
