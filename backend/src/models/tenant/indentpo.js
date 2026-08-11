const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('indentpo', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    indent_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    finishedproduct_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_qty: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    contract_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    issued_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    },
    machine_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    issue_date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'indentpo',
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
