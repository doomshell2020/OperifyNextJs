const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('productionsheet_item', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    productionseet_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    contractid: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    process_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    product_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    quantity: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    material: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    consumption: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    }
  }, {
    sequelize,
    tableName: 'productionsheet_item',
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
