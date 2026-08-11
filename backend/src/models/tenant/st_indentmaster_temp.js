const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_indentmaster_temp', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    indent_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    size_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    sale_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    quantity: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    unit_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    indent_status: {
      type: DataTypes.ENUM('A','R'),
      allowNull: false,
      defaultValue: "A"
    },
    pace: {
      type: DataTypes.ENUM('F','A','S'),
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    added_by: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: false
    },
    updated_time: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_indentmaster_temp',
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
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
